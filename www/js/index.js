$('document').ready(function(){
    console.log("hello");
    $('#list_persons').html('');
    for (var i = 0; i < gt.length; i++) {
        var p = gt[i];
        // console.log(p);
        var title = '' + p.born_year + '';
        if (p.year_of_death) {
            title += ' - ' + p.year_of_death;
        }
        title += ': ' + p.name;
        $('#list_persons').append('<a class="dropdown-item" href="' + p.page + '">' + title + '</a>');
    }
});
