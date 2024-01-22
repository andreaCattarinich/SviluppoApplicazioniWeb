fetch('navbar_homepage.html')
  .then(response => response.text())
  .then(data =>{
    document.getElementById('my_navbar').innerHTML = data;
  });

  fetch('footer.html')
  .then(response => response.text())
  .then(data =>{
    document.getElementById('my_footer').innerHTML = data;
});
