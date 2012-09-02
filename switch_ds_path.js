function switch_ds_path()
{
    if(document.ds.use_ds_path.checked)
    {
        document.ds.ds_path.disabled=false;
        document.getElementById('URL').style.display='none';
        document.getElementById('file').style.display='block';
    }
    else
    {
        document.ds.ds_path.disabled=true;
        document.getElementById('URL').style.display='block';
        document.getElementById('file').style.display='none';
    }
}
