<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>5202_Lab_5</title>
    </head>
    <body>
        <header>
            <h1>5202_Lab_5 - Form Validation Library</h1>
        </header>
        <main>
            <!-- Demo 1 -->
            <fieldset>
                <legend>Demo 1: Validate one item</legend>
                <p><?=$demo_1_info?></p>
                <!-- Email -->
                <form name="demo_1_email_form" action="index.php" method="post">
                    <p>***** Email cannot be empty, and its format will be validated *****</p>
                    <p>
                        <label>Email:</label>
                        <input type="text" name="user_email" />
                        <input type="submit" name="email_submit" value="Check IT" />
                    </p>
                </form>
                <!-- Integer -->
                <form name="demo_1_int_form" action="index.php" method="post">
                    <p>***** This demo call typeCheck method to check user input is an integer *****</p>
                    <p>
                        <label>Integer:</label>
                        <input type="text" name="user_int_value" />
                        <input type="submit" name="int_value_submit" value="Check IT" />
                    </p>
                </form>
                <!-- length -->
                <form name="demo_1_length_form" action="index.php" method="post">
                    <p>***** This demo call lengthCheck method to check user input a string with 3 letters *****</p>
                    <p>
                        <label>String:</label>
                        <input type="text" name="user_string_value" />
                        <input type="submit" name="string_value_submit" value="Check IT" />
                    </p>
                </form>
            </fieldset>
            <br/>
            <!-- Demo 2 -->
            <fieldset>
                <legend>Demo 2: Validate multi-items</legend>
                <p><?=$demo_2_info?></p>
                <form name="user_info_form" action="index.php" method="post">
                    <!-- Account -->
                    <p>***** Account cannot be empty, it starts with letter, and its length is between 5 to 15, it can include number and underscore *****</p>
                    <p>
                    <p>
                        <label>Account:</label>
                        <input type="text" name="user_account" value="<?=$user_account?>" />
                    </p>
                    <!-- Phone number -->
                    <p>***** Phone number cannot be empty, it is 10 or 11 numbers *****</p>
                    <p>
                        <label>Phone number:</label>
                        <input type="text" name="user_number" value="<?=$user_number?>" />
                    </p>
                    <input type="submit" name="user_submit" value="Check IT"/>
                </form>
            </fieldset>
        </main>
    </body>
</html>