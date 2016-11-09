<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Aktionen</h4>
    </div>
    <div class="panel-body">    
       <label>Bauteil löschen:</label>
        <form  class="form-inline" action="" method="post">
           <div class="form-group">     
                <input type="hidden" name="pid" value="{$pid}">
                <button type="submit" class="btn btn-danger" name="delete_part">Lösche Teil!</button>
                <div class="checkbox checkbox-danger">
                    <input type="checkbox" class="styled" name="delete_files_from_hdd">
                    <label for="delete_files_from_hdd" class="text-danger">Dateien dieses Bauteiles, die von keinem anderen Bauteil verwendet werden, auch von der Festplatte löschen</label>
                </div>
            </div>
        </form>
                
        <p></p>
                
        <label>Weiteres Bauteil anlegen:</label>
        <form action="" method="post">
            <div class="form-group">
                <input type="hidden" name="pid" value="{$pid}">
                <button class="btn btn-default" type="submit" name="add_one_more_part">Neues Bauteil erfassen</button>
            </div>
        </form>
    </div>
</div>
