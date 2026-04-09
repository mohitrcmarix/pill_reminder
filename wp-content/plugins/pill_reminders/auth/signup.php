<?php

function signup()
{

ob_start();
if (isset($_GET['reg_error'])) {
    echo '<p style="color:red; text-align:center;">' . esc_html(urldecode($_GET['reg_error'])) . '</p>';
}
?>

<div class="form_wrapper">
    <div class="form_container">
        <div class="title_container">
            <h2>Registration Form</h2>
        </div>
        <div class="row clearfix">
            <form method="post" action="" autocomplete="off">
                <div class="row clearfix">
                    <div class="col_half">
                        <div class="input_field"> <span><i aria-hidden="true" class="fa fa-user"></i></span>
                            <input type="text" name="first_name" placeholder="First Name" />
                        </div>
                    </div>
                    <div class="col_half">
                        <div class="input_field"> <span><i aria-hidden="true" class="fa fa-user"></i></span>
                            <input type="text" name="last_name" placeholder="Last Name" />
                        </div>
                    </div>
                </div>
                <div class="input_field"> <span><i aria-hidden="true" class="fa fa-envelope"></i></span>
                    <input type="text" name="username" placeholder="Username" />
                </div>
                <div class="input_field"> <span><i aria-hidden="true" class="fa fa-envelope"></i></span>
                    <input type="email" name="email" placeholder="Email" autocomplete="off" />
                </div>
                <div class="input_field"> <span><i aria-hidden="true" class="fa fa-lock"></i></span>
                    <input type="password" id="password" name="password" placeholder="Password"
                        autocomplete="new-password" />
                    <button type="button" id="togglePassword">Show</button>

                </div>
                <div class="input_field"> <span><i aria-hidden="true" class="fa fa-lock"></i></span>
                    <input type="password" id="confirmPassword" name="confirm_password"
                        placeholder="Re-type Password" />
                    <button type="button" id="toggleConfirmPassword">Show</button>
                </div>
                <div class="input_field radio_option">
                    <input type="radio" name="radiogroup1" id="rd1" value="male">
                    <label for="rd1">Male</label>
                    <input type="radio" name="radiogroup1" id="rd2" value="female">
                    <label for="rd2">Female</label>
                </div>
                <div class="input_field checkbox_option">
                    <input type="checkbox" id="cb1" name="terms">
                    <label for="cb1">I agree with terms and conditions</label>
                </div>

                <input class="button" type="submit" name="signup" value="Register" />
            </form>
        </div>
    </div>
</div>
<?php

    return ob_get_clean();

}
?>