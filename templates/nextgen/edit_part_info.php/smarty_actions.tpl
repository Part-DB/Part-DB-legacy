{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-bolt" aria-hidden="true"></i>
        {t}Aktionen{/t}
    </div>
    <div class="panel-body">    
        <form class="form-horizontal no-progbar" action="{$relative_path}edit_part_info.php" method="post">
            <div class="form-group">     
                <label class="control-label col-sm-2">{t}Bauteil löschen:{/t}</label> 
                <input type="hidden" name="pid" value="{$pid}">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-danger" name="delete_part">
                        <i class="fa fa-trash" aria-hidden="true"></i> Lösche Teil!
                    </button>
                    <div class="checkbox checkbox-danger">
                        <input type="checkbox" class="styled" id="delete_files_from_hdd" name="delete_files_from_hdd">
                        <label for="delete_files_from_hdd" class="text-danger">{t}Dateien dieses Bauteiles, die von keinem anderen Bauteil verwendet werden, auch von der Festplatte löschen{/t}</label>
                    </div>
                </div>
            </div>
        </form>
       
        <p></p>
                
        
        <form action="{$relative_path}edit_part_info.php" class="form-horizontal no-progbar" method="post">
            <div class="form-group">
                <label  class="control-label col-sm-2">{t}Weiteres Bauteil anlegen:{/t}</label>
                <input type="hidden" name="pid" value="{$pid}">
                <div class="col-sm-10">
                    <button class="btn btn-primary" type="submit" name="add_one_more_part"><i class="fa fa-plus-square" aria-hidden="true"></i> {t}Neues Bauteil erfassen{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
