<?php
session_start();
require_once '../mino/db_helper.php';
$helper = DbHelper::getInstance();
if (isset($_SESSION["s_user"]) && isset($_SESSION["s_user_id"]) && $helper->isUernameRegistered($_SESSION["s_user"]))
    header("Location: ../mino/notes.php");
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
    <title>Mino - Login</title>
</head>

<body>

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="light" viewBox="0 0 24 24">
            <path
                d="M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8M12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z" />
        </symbol>
        <symbol id="dark" viewBox="0 0 24 24">
            <path
                d="M18.73,18C15.4,21.69 9.71,22 6,18.64C2.33,15.31 2.04,9.62 5.37,5.93C6.9,4.25 9,3.2 11.27,3C7.96,6.7 8.27,12.39 12,15.71C13.63,17.19 15.78,18 18,18C18.25,18 18.5,18 18.73,18Z" />
        </symbol>
        <symbol id="show" viewBox="0 0 24 24">
            <path
                d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />
        </symbol>
        <symbol id="hide" viewBox="0 0 24 24">
            <path
                d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z" />
        </symbol>
    </svg>

    <div class="card mb-3 position-absolute top-50 start-50 translate-middle shadow-lg" style="max-width: 540px;">
        <div class="row g-0">
            <div class="col-md-4 position-relative">
                <svg id="theme_toggler" class="bi me-2 position-absolute top-0 start-0"
                    style="margin-top: 10px; margin-left: 10px;" width="40" height="40" role="img">
                    <use xlink:href="#dark" />
                </svg>
                <img src="../assets/img/rainbow-gradient.png"
                    class="img-fluid rounded-start object-fit-cover h-100 w-100" alt="...">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title text-center fw-semibold unselectable-text">Login to Mino</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary unselectable-text">Login to edit your saved notes or to save some
                    </h6>

                    <form action="" method="post" id="login_form">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" placeholder="Username" required>
                            <label for="username">Username</label>
                        </div>
                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" placeholder="Password"
                                    required>
                                <label for="password">Password</label>
                            </div>
                            <button id="login_password_revealer" type="button" class="btn btn-outline-secondary">
                                <svg class="bi img-fluid object-fit-cover h-100 w-100" width="32" height="32">
                                    <use xlink:href="#show" />
                                </svg>
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a id="cud_triggerer" class="card-link unselectable-text" data-bs-toggle="modal"
                                data-bs-target="#create_user_dialog">Create account</a>
                            <button type="submit" class="btn btn-outline-success fw-semibold">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_user_dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3 shadow card">
                <div class="card-header text-center unselectable-text">Create new account</div>
                <form class="modal-body p-4 card-body" action="" method="post" id="cud_form">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="cud_username" placeholder="Username" required>
                        <label for="cud_username">Username for the new account</label>
                    </div>
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="cud_password" placeholder="Password"
                                required>
                            <label for="cud_password">Password</label>
                        </div>
                        <button id="cud_password_revealer" class="btn btn-outline-secondary" type="button">
                            <svg class="bi img-fluid object-fit-cover h-100 w-100" width="32" height="32">
                                <use xlink:href="#show" />
                            </svg>
                        </button>
                    </div>
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="cud_r_password" placeholder="Password"
                                required>
                            <label for="cud_r_password">Repeat the password</label>
                        </div>
                        <button id="cud_r_password_revealer" class="btn btn-outline-secondary" type="button">
                            <svg class="bi img-fluid object-fit-cover h-100 w-100" width="32" height="32">
                                <use xlink:href="#show" />
                            </svg>
                        </button>
                    </div>
                </form>
                <div class="modal-footer flex-nowrap p-0">
                    <button class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end"
                        id="create_user_dialog_ok_btn" type="submit"><strong>Add user</strong></button>
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
    <script src="../mino/js/login.js"></script>
</body>

</html>