<?php
 include ('../config.php');
?>

function ms_kcf_selector_single(field, dir, window_title)
{
  window.KCFinder = {};
  window.KCFinder.callBack = function(url) {
    var s_dir = '';
    if (dir.length > 0)
    { s_dir = dir + '/'; }
    var name_array = url.split('<?php echo UPLOAD_URL_BASE; ?>' + s_dir);
    field.value = name_array[name_array.length - 1];
  };
  window.open('kcfinder/browse.php?type=files&amp;dir=files/' + dir, '');
}

function ms_kcf_photo_selector(field)
{
  ms_kcf_selector_single(field, '', 'Photo Selector');
}

function ms_kcf_techrep_selector(field)
{
  ms_kcf_selector_single(field, 'techrep', 'Tech Report Selector');
}

function ms_kcf_seminar_selector(field)
{
  ms_kcf_selector_single(field, 'seminar', 'Seminar Selector');
}

function ms_kcf_workshop_selector(field)
{
  ms_kcf_selector_single(field, '', 'Workshop Selector');
}

function ms_kcf_multiple_files() {
    window.KCFinder = {};
    window.KCFinder.callBackMultiple = function(files) {
        window.KCFinder = null;
        for (var i; i < files.length; i++) {
            // Actions with files[i] here
        }
    };
    window.open('kcfinder/browse.php', 'kcfinder_multiple');
}

