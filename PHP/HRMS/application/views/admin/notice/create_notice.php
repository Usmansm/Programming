
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="wrap-fpanel">
            <div class="panel panel-default" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo $this->language->form_heading()[22] ?></strong>
                    </div>                
                </div>
                <div class="panel-body">

                    <form role="form" id="form" action="<?php echo base_url(); ?>admin/notice/save_notice/<?php echo $notice->notice_id; ?>" method="post" class="form-horizontal form-groups-bordered">
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[23][0] ?> <span class="required">*</span></label>

                            <div class="col-sm-2"><input type="checkbox" class="select_one" name="flag" value="1"
                                <?php
                                if ($notice->flag == 1) {
                                    ?>
                                                             checked
                                                             <?php
                                                         }
                                                         ?>> Published</div>
                            <div class="col-sm-2"><input type="checkbox" class="select_one" name="flag" value="0"
                                <?php
                                if ($notice->flag == 0) {
                                    ?>
                                                             checked
                                                             <?php
                                                         }
                                                         ?>> UnPublished</div>

                        </div>

                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[23][1] ?> <span class="required">*</span></label>

                            <div class="col-sm-8">
                                <input type="text" name="title" value="<?php echo $notice->title; ?>" class="form-control" requried placeholder="Enter Notice Title Here"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[23][2] ?> <span class="required">*</span></label>

                            <div class="col-sm-8">
                                <textarea name="short_description" class="form-control" required placeholder="Enter Short Description"><?php echo $notice->short_description; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[23][3] ?> <span class="required">*</span></label>
                            <div class="col-sm-8">
                                <textarea class="form-control " name="long_description" id="ck_editor" required><?php echo $notice->long_description; ?></textarea>
                                <?php echo display_ckeditor($editor['ckeditor']); ?>
                            </div>
                        </div>

                        <!--hidden input values -->                       

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" id="sbtn" class="btn btn-primary"><?php echo $this->language->from_body()[1][12] ?></button>                            
                            </div>
                        </div>   
                    </form>
                </div>            
            </div>
            <br/>   
        </div>   
    </div>   
</div>