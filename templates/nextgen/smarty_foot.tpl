            {if isset($messages)}
                <div class="panel panel-default">
                    <form action="" method="post" class="panel-body">
                        {foreach $messages as $msg}
                            {if isset($msg.text)}
                                {if isset($msg.strong)}<strong>{/if}
                                {if isset($msg.color)}<span style="color: {$msg.color}; ">{/if}
                                {$msg.text nofilter}
                                {if isset($msg.color)}</span>{/if}
                                {if isset($msg.strong)}</strong>{/if}
                            {/if}

                            {if isset($msg.html)}
                                {$msg.html nofilter}
                            {/if}

                            {if !isset($msg.no_linebreak)}<br>{/if}
                        {/foreach}
                    </form>
                </div>
            {/if}

            </div> <!-- content-data -->
         </div> <!-- .container-float -->
      </div> <!-- page-content-wrapper -->

      </main>

</div>   <!-- Wrapper -->
  
  <!-- Back to top button -->
  <a id="back-to-top" href="#" class="btn btn-primary back-to-top link-anchor" role="button" title="Zum Seitenbeginn" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>
        
   </body>

</html>
