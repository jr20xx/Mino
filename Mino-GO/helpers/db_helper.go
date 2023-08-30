package helpers

import (
	"database/sql"
	"encoding/json"
	_ "github.com/go-sql-driver/mysql"
	"github.com/gorilla/sessions"
	"log"
	"net/http"
	"time"
)

var (
	server   = "localhost"
	db_name  = "mino"
	username = "test"
	password = ""
	db       *sql.DB
)

type ServerInternals struct {
	Session        *sessions.Session
	ResponseWriter http.ResponseWriter
	Request        *http.Request
}

type Note struct {
	ID        int    `json:"ID"`
	Title     string `json:"TITLE"`
	Body      string `json:"BODY"`
	TimeStamp int    `json:"TIME_STAMP"`
	UserID    int    `json:"USER_ID"`
}

type responseStatus struct {
	Status int `json:"status"`
}

func InitDatabase() {
	var err error
	log.Println("Establishing a connection with the database server...")
	db, err = sql.Open("mysql", username+":"+password+"@tcp("+server+")/")
	if err != nil {
		log.Fatal("Aborting: connection failed with the following error:\n", err.Error())
	}

	log.Println("Creating the database if it does not exist already...")
	_, err = db.Exec("CREATE DATABASE IF NOT EXISTS " + db_name + ";")
	if err != nil {
		log.Fatal("Aborting: failed to create the database with the following error:\n", err)
	}

	log.Println("Establishing a connection with the database...")
	db, err = sql.Open("mysql", username+":"+password+"@tcp("+server+")/"+db_name)
	if err != nil {
		log.Fatal("Aborting: connection with the database failed with the following error:\n", err.Error())
	}

	log.Println("Recreating the structure of the table USERS...")
	_, err = db.Exec("CREATE TABLE IF NOT EXISTS mino.USERS(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, USERNAME TEXT NOT NULL UNIQUE, PASSWORD TEXT NOT NULL);")
	if err != nil {
		log.Fatal("Aborting: failed to recreate the structure of the USERS table with the following error:\n", err)
	}

	log.Println("Recreating the structure of the table NOTES...")
	_, err = db.Exec("CREATE TABLE IF NOT EXISTS mino.NOTES(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, TITLE TEXT NOT NULL, BODY TEXT NOT NULL, TIME_STAMP INTEGER NOT NULL, USER_ID INTEGER NOT NULL, FOREIGN KEY (USER_ID) REFERENCES mino.USERS(ID) ON DELETE CASCADE);")
	if err != nil {
		log.Fatal("Aborting: failed to recreate the structure of the NOTES table with the following error:\n", err)
	}
}

func isUsernameRegistered(username string) bool {
	statement, err := db.Prepare("SELECT COUNT(*) FROM mino.USERS WHERE USERNAME = ?")
	if err == nil {
		defer statement.Close()
		var count int
		err = statement.QueryRow(username).Scan(&count)
		if err == nil {
			return count > 0
		}
	}
	return false
}

func CheckCredentials(username string, password string, serverInternals ServerInternals) ([]byte, error) {
	rows, err := db.Query("SELECT * FROM USERS WHERE USERNAME=? AND PASSWORD=?;", username, password)
	if err == nil {
		defer rows.Close()
		if rows.Next() {
			var (
				userID   int
				username string
				password string
			)
			err = rows.Scan(&userID, &username, &password)
			if err == nil {
				serverInternals.Session.Values["username"] = username
				serverInternals.Session.Values["user_id"] = userID
				serverInternals.Session.Save(serverInternals.Request, serverInternals.ResponseWriter)
				return json.Marshal(responseStatus{Status: http.StatusOK})
			}
		} else {
			return json.Marshal(responseStatus{Status: http.StatusUnauthorized})
		}
	}
	return nil, err
}

func DeauthUser(serverInternals ServerInternals) ([]byte, error) {
	serverInternals.Session.Values["username"] = nil
	serverInternals.Session.Values["user_id"] = nil
	err := serverInternals.Session.Save(serverInternals.Request, serverInternals.ResponseWriter)
	if err == nil {
		return json.Marshal(responseStatus{Status: http.StatusOK})
	}
	return nil, err
}

func RegisterUser(username string, password string) ([]byte, error) {
	if isUsernameRegistered(username) {
		return json.Marshal(responseStatus{Status: 400})
	} else {
		result, err := db.Exec("INSERT INTO USERS(USERNAME, PASSWORD) VALUES(?, ?)", username, password)
		if err == nil {
			var affectedRowsCount int64
			affectedRowsCount, err = result.RowsAffected()
			if affectedRowsCount > 0 {
				return json.Marshal(responseStatus{Status: http.StatusCreated})
			}
		}
		return nil, err
	}
}

func ChangePassword(user_id int, password string, newPassword string) ([]byte, error) {
	result, err := db.Exec("UPDATE USERS SET PASSWORD=? WHERE ID=? AND PASSWORD=?;", newPassword, user_id, password)
	if err == nil {
		rowsAffectedCount, _ := result.RowsAffected()
		if rowsAffectedCount > 0 {
			return json.Marshal(responseStatus{Status: http.StatusOK})
		} else {
			return json.Marshal(responseStatus{Status: http.StatusBadRequest})
		}
	}
	return nil, err
}

func RemoveAccount(user_id int, serverInternals ServerInternals) ([]byte, error) {
	result, err := db.Exec("DELETE FROM USERS WHERE ID=?;", user_id)
	if err == nil {
		var rowsAffected int64
		rowsAffected, err = result.RowsAffected()
		if rowsAffected > 0 {
			DeauthUser(serverInternals)
			return json.Marshal(responseStatus{Status: http.StatusOK})
		}
	}
	return nil, err
}

func CreateNote(title string, body string, user_id int) ([]byte, error) {
	result, err := db.Exec("INSERT INTO NOTES(TITLE, BODY, TIME_STAMP, USER_ID) VALUES(?, ?, ?, ?);", title, body, time.Now().Unix(), user_id)
	if err == nil {
		var rows *sql.Rows
		noteID, _ := result.LastInsertId()
		rows, err = db.Query("SELECT * FROM NOTES WHERE ID=?;", noteID)
		if err == nil {
			defer rows.Close()
			if rows.Next() {
				var note Note
				err = rows.Scan(&note.ID, &note.Title, &note.Body, &note.TimeStamp, &note.UserID)
				if err == nil {
					return json.Marshal(note)
				}
			}
		}
	}
	return nil, err
}

func GetNotes(user_id int) ([]byte, error) {
	rows, err := db.Query("SELECT * FROM NOTES WHERE USER_ID=? ORDER BY TIME_STAMP;", user_id)
	if err == nil {
		defer rows.Close()
		notes := []Note{}
		for rows.Next() {
			var note Note
			err = rows.Scan(&note.ID, &note.Title, &note.Body, &note.TimeStamp, &note.UserID)
			if err == nil {
				notes = append(notes, note)
			}
		}
		return json.Marshal(notes)
	}
	return nil, err
}

func UpdateNote(note_id int, title string, body string, user_id int) ([]byte, error) {
	result, err := db.Exec("UPDATE NOTES SET TITLE=?, BODY=?, TIME_STAMP=? WHERE ID=? AND USER_ID=?;", title, body, time.Now().Unix(), note_id, user_id)
	if err == nil {
		var rows *sql.Rows
		rowsAffectedCount, _ := result.RowsAffected()
		if rowsAffectedCount > 0 {
			rows, err = db.Query("SELECT * FROM NOTES WHERE ID=?;", note_id)
			if err == nil {
				defer rows.Close()
				if rows.Next() {
					var note Note
					err = rows.Scan(&note.ID, &note.Title, &note.Body, &note.TimeStamp, &note.UserID)
					if err == nil {
						return json.Marshal(note)
					}
				}
			}
		}
	}
	return nil, err
}

func RemoveNote(note_id int, user_id int) ([]byte, error) {
	result, err := db.Exec("DELETE FROM NOTES WHERE ID=? AND USER_ID =?", note_id, user_id)
	if err == nil {
		var rowsAffected int64
		rowsAffected, err = result.RowsAffected()
		if rowsAffected > 0 {
			return json.Marshal(responseStatus{Status: http.StatusOK})
		}
	}
	return nil, err
}
