<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/main.css">
</head>

<body>

    <form class="form" action="process-form.php" method="post" novalidate>
        <div class="form__group">
            <label for="name" class="form__label">Name</label>
            <input type="text" id="name" name="name" class="form__input" required aria-required="true"
                aria-label="Name">
        </div>
        <div class="form__group">
            <label for="email" class="form__label">Email</label>
            <input type="email" id="email" name="email" class="form__input" required aria-required="true"
                aria-label="Email">
        </div>
        <div class="form__group">
            <label for="password" class="form__label">Password</label>
            <input type="password" id="password" name="password" class="form__input" required aria-required="true"
                aria-label="Password">
        </div>
        <button type="submit" class="form__button">Register</button>
    </form>

</body>

</html>
