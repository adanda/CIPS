function projectToggle($build)
{
    if ($('#build_'+$build).is(':visible')) {
        $('#image_'+$build).attr('src', '/images/arrow_closed.png');
    } else {
        $('#image_'+$build).attr('src', '/images/arrow_open.png');
    }
    
    $('#build_'+$build).slideToggle();
}

function loadBuildResults($project_slug, $number)
{
    // number of shown builds is the number of li's - 1 for the header
    var $shown_builds = $('#builds_list li').size() - 1;
    
    $.ajax({
        url: '/loadbuilds/'+$project_slug+'/'+$number+'/'+$shown_builds,
        success: function($html){
            $('#builds_list').append($html);
            $('#builds_list li').animate({opacity: 1}, 2000);
        }
    });
}

function buildProject($project_slug)
{
    $('#loader_build').show();
    $.ajax({
        url: '/build/'+$project_slug,
        success: function($html){
            $('#loader_build').hide();
            $('#builds_list').children().first().after($html);
            $('#builds_list li').animate({opacity: 1}, 2000);
        }
    });
}