{locale path="nextgen/locale" domain="partdb"}
<div class="card border-success">
    <div class="card-header bg-success text-white"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;
        {t}Installation: Fertigstellung{/t}</div>
    <div class="card-body">
        <b><span style="color: green; ">{t escape=off}Herzlichen Glückwunsch, die Installation bzw. das Update von Part-DB ist fast abgeschlossen!<br>
                Weitere Einstellungen finden Sie unter dem Menüpunkt "System".{/t}</span></b>
        <p><b>{t}Sie können sich nun mit dem Nutzernamen "admin" und dem eben festgelegten Password einloggen.{/t}</b></p>
        <form action="index.php" method="post">
            <button class="btn btn-primary" type="submit" name="finish">{t}Fertigstellen{/t}</button>
        </form>
    </div>
</div>
