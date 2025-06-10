<?php
require_once 'includes/header.php';
?>
<main class="info-page">
    <h1>Contact Us</h1>
    <p>If you have any questions, suggestions, or need support, please reach out to us:</p>
    <ul>
        <li>Email: <a href="mailto:Bersufeqad@gmail.com">Bersufeqad@gmail.com</a></li>
        <li>Telegram: <a href="https://t.me/hehe69" target="_blank">@hehe69</a></li>
    </ul>
    <form method="post" action="#" style="margin-top: 20px;">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br>
        <button type="submit">Send</button>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?> 