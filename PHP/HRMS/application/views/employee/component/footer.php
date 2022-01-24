<footer class="col-sm-12 footer">      
    <div class="container">
        <div class="row">

            <p class="text-center margin">
                Designed and Developed by <a href="http://fratelloneit.com" target="_blank">Fratellone IT Solutions LTD.</a>
            </p>

        </div>
    </div>
</footer>                    
<script type="text/javascript">
    $(document).ready(function() {

    });
</script>
<!-- ALl Custom Scripts -->  
<script src="<?php echo base_url(); ?>asset/js/jquery-1.10.2.min.js"></script>   
<script src="<?php echo base_url(); ?>asset/js/custom.js"></script>
<script src="<?php echo base_url() ?>asset/js/select2.js"></script>

<script src="<?php echo base_url(); ?>asset/js/custom-validation.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>asset/js/jquery.validate.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>asset/js/bootstrap.min.js" type="text/javascript"></script>    
<script src="<?php echo base_url() ?>asset/js/jasny-bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>asset/js/timepicker.js" ></script>  
<script src="<?php echo base_url() ?>asset/js/bootstrap-datepicker.js" ></script> 

<!-- Data Table -->
<script src="<?php echo base_url(); ?>asset/js/plugins/metisMenu/metisMenu.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>asset/js/plugins/dataTables/jquery.dataTables.js" type="text/javascript"></script>  
<script src="<?php echo base_url(); ?>asset/js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        setTimeout(function() {
            $(".alert").fadeOut("slow", function() {
                $(".alert").remove();
            });

        }, 3000);
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
        });
    });
</script>
</body>
</html>