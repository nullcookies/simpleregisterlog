<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link href="/favicon.ico" rel="shortcut icon">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->context->getConfigVal('title'); ?></title>

        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <!--[if lt IE 9]>
            <script src="/js/html5shiv.min.js"></script>
            <script src="/js/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="/js/chosen/chosen.css">
        <link rel="stylesheet" href="/css/jasny-bootstrap.min.css">
        <link rel="stylesheet" href="/css/context.bootstrap.css">
        <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="/css/bootstrap-colorpicker.min.css">
        <link rel="stylesheet" href="/css/stat.css">

        <!--<link rel="stylesheet" href="/css/map.css">-->
        
        <script type="text/javascript" src="/js/jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/js/jquery.file.upload/jquery.fileupload.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/moment-with-locales.js"></script>
        <script src="/js/jasny-bootstrap.min.js"></script>
        <script src="/js/bootstrap-datetimepicker.min.js"></script>
        <script src="/js/bootstrap-colorpicker.min.js"></script>
                
        <script type="text/javascript" src="/js/interact-1.2.0.min.js"></script>
        <script type="text/javascript" src="/js/jquery.svg.package-1.5.0/jquery.svg.min.js"></script>
        <script type="text/javascript" src="/js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="/js/context.js"></script>
        
        <script src="/js/chosen/chosen.jquery.js" type="text/javascript"></script>
        <script src="/js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
        
        <script src="/js/highcharts.js"></script>
                
        <script type="text/javascript" src="/js/stat.js"></script>
        <script type="text/javascript" src="/js/stat-base.js"></script>
        <!--<script type="text/javascript" src="/js/map.js"></script>-->
    </head>
    <body>
    
    <div id="header">
        <a href="/" id="logo"></a>
    </div>
    
    <div class="navbar navbar-static-top" role="navigation">
        <?php echo $menu; ?>
    </div>
    
    <div class="info-content"><?php echo $content; ?></div>
    
    <div class="navbar-fixed-bottom row-fluid">
        <div class="navbar-inner">
            <div class="container">
                
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="mainModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

  </body>
  
    <script type="text/javascript">
        var config = {
            '.chosen-select'           : { width: 'auto', min_width: '100px' },
            '.chosen-select-deselect'  : {allow_single_deselect:true},
            '.chosen-select-no-single' : {disable_search_threshold:10},
            '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            '.chosen-select-width'     : {width: "95%"}
        }
        for (var selector in config) {
            $(selector).each(function() { $(this).chosen(config[selector]); });
        }
    </script>
</html>
