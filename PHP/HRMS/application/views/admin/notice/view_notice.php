
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="wrap-fpanel">

    <div class="row">
        <div class="col-sm-12" data-offset="0">                            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo $this->language->form_heading()[23] ?></strong>
                    </div>
                </div>
                <!-- Table -->

                <table class="table table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>SL</th> 
                            <th class="col-sm-1">Created Date</th>                                     
                            <th>Title</th>                                     
                            <th class="col-sm-5">Short Description</th>                                                                                
                            <th>Status</th>                                                                                
                            <th class="col-sm-2">Action</th>                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php $key = 1; ?>
                        <?php if (!empty($notice)): foreach ($notice as $v_notice): ?>
                                <tr>
                                    <td><?php echo $key; ?></td>                        
                                    <td><?php echo date('d-M-Y', strtotime($v_notice->created_date)); ?></td>
                                    <td><?php echo $v_notice->title; ?></td>
                                    <td><?php 
                                        $str = strlen($v_notice->short_description);
                                        if ($str > 80) {
                                            $ss = '<strong> ......</strong>';
                                        } else {
                                            $ss = '&nbsp';
                                        } echo substr($v_notice->short_description, 0, 80) . $ss;
                                        ?></td>
                                    <td>
                                        <?php if ($v_notice->flag == 0) : ?> 
                                            <span class="label label-danger">Unpublished</span>
                                        <?php else : ?>                                        
                                            <span class="label label-success">Published</span>                                                                             
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo btn_view('admin/notice/notice_details/' . $v_notice->notice_id); ?>                                                                
                                        <?php echo btn_edit('admin/notice/add_notice/' . $v_notice->notice_id); ?>                                                                
                                        <?php echo btn_delete('admin/notice/delete_notice/' . $v_notice->notice_id); ?>                                                                
                                    </td>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
</div> 
