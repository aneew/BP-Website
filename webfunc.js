function loadPage(url) {
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById('content').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

function toggleNavbar() {
    var navbar = document.getElementById('navbar');
    navbar.classList.toggle('hidden');

    var content = document.getElementById('content');
    content.classList.toggle('shifted');
}
