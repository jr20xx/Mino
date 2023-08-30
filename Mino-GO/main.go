package main

import (
	"Mino-GO/helpers"
	"crypto/sha512"
	"encoding/hex"
	"fmt"
	"github.com/gorilla/sessions"
	"log"
	"net/http"
	"strconv"
	"text/template"
)

var (
	temp  *template.Template
	store *sessions.CookieStore
	port  = 8748
	user  UserInfo
)

type UserInfo struct {
	Username string
	UserID   int
}

func main() {
	helpers.InitDatabase()
	log.Println("Starting server now at http://localhost:" + fmt.Sprint(port) + "/")
	http.Handle("/assets/", http.StripPrefix("/assets", http.FileServer(http.Dir("assets"))))
	http.Handle("/mino/css/", http.StripPrefix("/mino/css", http.FileServer(http.Dir("mino/css"))))
	http.Handle("/mino/js/", http.StripPrefix("/mino/js", http.FileServer(http.Dir("mino/js"))))
	http.HandleFunc("/", serveFiles)
	store = sessions.NewCookieStore([]byte("12345678"))
	temp = template.Must(template.ParseGlob("mino/*.html"))
	log.Fatal(http.ListenAndServe(":"+fmt.Sprint(port), nil))
}

func serveFiles(w http.ResponseWriter, r *http.Request) {
	session, _ := store.Get(r, "mino_session")
	w.Header().Set("Cache-Control", "no-cache")
	if r.Method == "GET" {
		if session.Values["username"] == nil && session.Values["user_id"] == nil {
			temp.ExecuteTemplate(w, "login.html", nil)
			log.Println("Loaded login page")
		} else {
			user = UserInfo{
				Username: session.Values["username"].(string),
				UserID:   session.Values["user_id"].(int),
			}
			temp.ExecuteTemplate(w, "notes.html", struct{ UserInfo }{user})
			log.Println("Loaded notes page")
		}
	} else if r.Method == "POST" {
		err := r.ParseForm()
		if err != nil {
			http.Error(w, "Failed to parse request body with the following error:\n"+err.Error(), http.StatusBadRequest)
		} else {
			username := r.FormValue("username")
			password := r.FormValue("password")
			title := r.FormValue("title")
			body := r.FormValue("body")
			note_id, _ := strconv.Atoi(r.FormValue("note_id"))
			newPassword := r.FormValue("newPassword")
			action := r.FormValue("action")

			if password != "" {
				hash := sha512.Sum512([]byte(password))
				password = hex.EncodeToString((hash)[:])
			}

			if newPassword != "" {
				hash := sha512.Sum512([]byte(newPassword))
				newPassword = hex.EncodeToString((hash)[:])
			}

			serverInternals := helpers.ServerInternals{
				Session:        session,
				ResponseWriter: w,
				Request:        r,
			}
			switch action {
			case "authenticate":
				jsonResult, err := helpers.CheckCredentials(username, password, serverInternals)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: user authentication process failed with the following error:\n", err)
				} else {
					log.Println("Info: user authentication process completed successfully")
				}
			case "deauthenticate":
				jsonResult, err := helpers.DeauthUser(serverInternals)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: user deauthentication process failed with the following error:\n", err)
				} else {
					log.Println("Info: user deauthentication process completed successfully")
				}
			case "add_user":
				jsonResult, err := helpers.RegisterUser(username, password)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: user addition process failed with the following error:\n", err)
				} else {
					log.Println("Info: user addition process completed successfully")
				}
			case "update_password":
				jsonResult, err := helpers.ChangePassword(user.UserID, password, newPassword)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: passoword change process failed with the following error:\n", err)
				} else {
					log.Println("Info: password change process completed successfully")
				}
			case "remove_account":
				jsonResult, err := helpers.RemoveAccount(user.UserID, serverInternals)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: account removal process failed with the following error:\n", err)
				} else {
					log.Println("Info: account removal process completed successfully")
				}
			case "create_note":
				jsonResult, err := helpers.CreateNote(title, body, user.UserID)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: note creation process failed with the following error:\n", err)
				} else {
					log.Println("Info: note creation process completed successfully")
				}
			case "get_notes":
				jsonArrayResult, err := helpers.GetNotes(user.UserID)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonArrayResult)
				if err != nil {
					log.Println("Error: notes' fetch process failed with the following error:\n", err)
				} else {
					log.Println("Info: notes fetch process completed successfully")
				}
			case "update_note":
				jsonResult, err := helpers.UpdateNote(note_id, title, body, user.UserID)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonResult)
				if err != nil {
					log.Println("Error: note update process failed with the following error:\n", err)
				} else {
					log.Println("Info: note update process completed successfully")
				}
			case "remove_note":
				jsonArrayResult, err := helpers.RemoveNote(note_id, user.UserID)
				w.Header().Set("Content-Type", "application/json")
				w.Write(jsonArrayResult)
				if err != nil {
					log.Println("Error: note removal process failed with the following error:\n", err)
				} else {
					log.Println("Info: note removal process completed successfully")
				}
			default:
				w.Header().Set("Content-Type", "text/plain")
				w.WriteHeader(http.StatusNotImplemented)
			}
		}
	}
}
