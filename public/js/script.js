$(function() {
    var url = window.location.pathname;
    var url1 = url.replace('_add','').replace('_view','').split( '/' );
    var url2 = url1[1];
    $("ul.tree li a[href*='"+url2+"']").parents().addClass('active');

    console.log(url);
    console.log(url1);
    console.log(url2);
});
