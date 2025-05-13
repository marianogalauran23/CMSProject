<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap');

    nav {
        background-color: #0e0d0d;
        color: white;
        height: 9vh;
        width: 100%;
        margin: 0;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;

        font-family: Arial, sans-serif;
        font-family: "Open Sans", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
        position: fixed;
        z-index: 300;
    }

    nav img {
        user-select: none;
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 24px;
        flex-direction: row;
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    nav ul li {
        display: inline;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease-in-out, font-size 0.3s ease-in-out, font-weight 0.3s ease-in-out;
    }

    nav ul li a:hover {
        color: bisque;
        font-size: 1.1rem;
        font-weight: 700;
    }
</style>

<nav>
    <img src="../assets/logo_dark.png" alt="Logo" draggable="false" style="width: 170px; height: 150px;">
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="services.html">Documentation</a></li>
        <li><a href="contact.html">Contact</a></li>
    </ul>
</nav>