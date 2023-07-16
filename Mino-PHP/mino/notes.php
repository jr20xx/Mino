<?php
session_start();
if ($_SESSION["s_user"] === null && $_SESSION["s_user_id"] === null)
    header("Location: ../mino/login.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href="../mino/css/styles.css" rel="stylesheet">
    <title>
        <?php echo $_SESSION["s_user"]; ?> - Mino
    </title>
</head>

<body id="pageBody">

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="menu" viewBox="0 0 24 24">
            <path d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z" />
        </symbol>
        <symbol id="save" viewBox="0 0 24 24">
            <path
                d="M17 3H5C3.89 3 3 3.9 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V7L17 3M19 19H5V5H16.17L19 7.83V19M12 12C10.34 12 9 13.34 9 15S10.34 18 12 18 15 16.66 15 15 13.66 12 12 12M6 6H15V10H6V6Z" />
        </symbol>
        <symbol id="delete" viewBox="0 0 24 24">
            <path
                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" />
        </symbol>
        <symbol id="close" viewBox="0 0 24 24">
            <path
                d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
        </symbol>
    </svg>

    <main class="d-flex">

        <div id="notes_list_container" class="d-flex flex-column flex-shrink-0 bg-secondary-subtle">
            <div class="mb-lg-0 d-flex align-items-center p-3 border-bottom">
                <div class="dropdown" style="margin-right: 5px;">
                    <img src="../assets/img/rainbow-gradient.png" alt="..." width="32" height="32"
                        data-bs-toggle="dropdown" aria-expanded="false" class="d-flex rounded-circle dropdown-toggle">
                    <ul class="dropdown-menu text-small shadow">
                        <li id="note_adder_button" class="dropdown-item unselectable-text" data-bs-toggle="modal"
                            data-bs-target="#note_writer_modal">New note...</li>
                        <li class="dropdown-item unselectable-text" id="theme_toggle">Change theme</li>
                        <li class="dropdown-item unselectable-text">Profile</li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li id="logout_button" data-bs-toggle="modal" data-bs-target="#actions_checker_dialog"
                            class="dropdown-item unselectable-text">Log out</li>
                    </ul>
                </div>
                <br><br>
                <input type="search" id="notes_searcher" class="search form-control fs-6" placeholder="Search notes..."
                    aria-label="Search">
            </div>

            <ul class="list-group list-group-flush scrollarea bg-dark-subtle" id="notes_list">
            </ul>
        </div>

        <div class="scrollarea vstack gap-3 bg-body-tertiary" style="width: 100%;" id="note_viewer_container">

            <div class="px-3 py-2 bg-secondary-subtle border-bottom">
                <div class="container">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                        <div id="note_viewer_list_toggler"
                            class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-decoration-none link-body-emphasis">
                            <svg class="bi me-2" width="40" height="32" role="img" aria-label="Drawer">
                                <use xlink:href="#menu" />
                            </svg>
                            <spam id="note_viewer_timestamp" class="one-line unselectable-text"
                                style="font-size: x-small;"></spam>
                        </div>

                        <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                            <li id="note_viewer_save_button" data-bs-toggle="modal"
                                data-bs-target="#actions_checker_dialog"
                                class="nav-link link-body-emphasis unselectable-text disabled">
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                                    <use xlink:href="#save" />
                                </svg>
                                Save
                            </li>
                            <li id="note_viewer_delete_button" data-bs-toggle="modal"
                                data-bs-target="#actions_checker_dialog"
                                class="nav-link link-body-emphasis unselectable-text">
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                                    <use xlink:href="#delete" />
                                </svg>
                                Delete
                            </li>
                            <li id="close_note_viewer_button" class="nav-link link-body-emphasis unselectable-text">
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                                    <use xlink:href="#close" />
                                </svg>
                                Close
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <form>
                <div class="input-group p-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="note_viewer_title" name="note_viewer_title"
                            placeholder="Title" />
                        <label for="note_viewer_title">Title</label>
                    </div>
                </div>

                <div class="input-group p-2">
                    <div class="form-floating">
                        <textarea class="form-control" id="note_viewer_body" name="note_viewer_body" placeholder="Text"
                            style="height: 250px; min-height: 250px;" required></textarea>
                        <label for="note_viewer_body">Text</label>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <div class="modal fade" id="note_writer_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3 shadow card">
                <div class="card-header text-center unselectable-text">Add a note</div>
                <form class="modal-body p-1 card-body" action="" method="post" id="note_writer_form">
                    <div class="input-group p-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="note_writer_title" name="note_writer_title"
                                placeholder="Title" required />
                            <label for="note_writer_title">Title</label>
                        </div>
                    </div>

                    <div class="input-group p-2">
                        <div class="form-floating">
                            <textarea class="form-control" id="note_writer_body" name="note_writer_body"
                                placeholder="Text" style="height: 200px; min-height: 200px;" required></textarea>
                            <label for="note_writer_body">Text</label>
                        </div>
                    </div>
                </form>
                <div class="modal-footer flex-nowrap p-0">
                    <button class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end"
                        id="note_adder_ok_btn" type="submit"><strong>Add note</strong></button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0"
                        data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="actions_checker_dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-body p-4 text-center">
                    <h5 id="actions_checker_dialog_title" class="mb-0 unselectable-text" style="padding-bottom: 10px;">
                    </h5>
                    <p id="actions_checker_dialog_message" class="mb-0 unselectable-text"></p>
                </div>
                <div class="modal-footer flex-nowrap p-0">
                    <button id="actions_checker_dialog_ok_btn" type="button"
                        class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end"><strong>Ok</strong></button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0"
                        data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/jquery/jquery.js"></script>
    <script src="../assets/jscookie/js.cookie.js"></script>
    <script src="../assets/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="../mino/js/notes.js"></script>
</body>

</html>