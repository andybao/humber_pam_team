<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>pam_team_demo_1</title>
    </head>
    <body>
        <header>
            <h1>Demo 1 - User input saving</h1>
        </header>
        <main>
            <form name="demo_1_form" action="index.php" method="post">
                <p>Input something here, then submit it</p>
                <input type="text" name="demo_input" value="<?=$user_input;?>" />
                <input type="submit" name="save_submit" value="Submit with input saving" />
                <input type="submit" name="normal_submit" value="Submit without input saving" />
            </form>
        </main>
    </body>
</html>