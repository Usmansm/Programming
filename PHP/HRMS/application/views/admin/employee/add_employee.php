<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<form role="form" id="employee-form" enctype="multipart/form-data" action="<?php echo base_url() ?>admin/employee/save_employee/<?php
if (!empty($employee_info->employee_id)) {
    echo $employee_info->employee_id;
}
?>" method="post" class="form-horizontal form-groups-bordered">    
    <div class="row">
        <div class="wrap-fpanel">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo $this->language->form_heading()[11] ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <!-- ************************ Personal Information Panel Start ************************-->
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $this->language->from_body()[12][0] ?></h4>
                </div>
                <div class="panel-body ">
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][1] ?> <span class="required"> *</span></label>
                        <input type="text" name="first_name" value="<?php
                        if (!empty($employee_info->first_name)) {
                            echo $employee_info->first_name;
                        }
                        ?>"  class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][2] ?> <span class="required"> *</span></label>
                        <input type="text" name="last_name" value="<?php
                        if (!empty($employee_info->last_name)) {
                            echo $employee_info->last_name;
                        }
                        ?>" class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][3] ?> <span class="required"> *</span></label>
                        <div class="input-group">
                            <input type="text" name="date_of_birth" value="<?php
                            if (!empty($employee_info->date_of_birth)) {
                                echo $employee_info->date_of_birth;
                            }
                            ?>" class="form-control datepicker" data-format="yyy-mm-dd">
                            <div class="input-group-addon">
                                <a href="#"><i class="entypo-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][4] ?> <span class="required"> *</span></label>
                        <select name="gender" class="form-control" >
                            <option value="">Select Gender ...</option>
                            <option value="Male" <?php
                            if (!empty($employee_info->gender)) {
                                echo $employee_info->gender == 'Male' ? 'selected' : '';
                            }
                            ?>>Male</option>
                            <option value="Female" <?php
                            if (!empty($employee_info->gender)) {
                                echo $employee_info->gender == 'Female' ? 'selected' : '';
                            }
                            ?>>Female</option>
                        </select>
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][5] ?><span class="required"> *</span></label>
                        <select name="maratial_status" class="form-control" >
                            <option value="">Select Status ...</option>
                            <option value="Married" <?php
                            if (!empty($employee_info->maratial_status)) {
                                echo $employee_info->maratial_status == 'Married' ? 'selected' : '';
                            }
                            ?>>Married</option>
                            <option value="Un-Married" <?php
                            if (!empty($employee_info->maratial_status)) {
                                echo $employee_info->maratial_status == 'Un-Married' ? 'selected' : '';
                            }
                            ?>>Un-Married</option>
                            <option value="Widowed" <?php
                            if (!empty($employee_info->maratial_status)) {
                                echo $employee_info->maratial_status == 'Widowed' ? 'selected' : '';
                            }
                            ?>>Widowed</option>
                            <option value="Divorced" <?php
                            if (!empty($employee_info->maratial_status)) {
                                echo $employee_info->maratial_status == 'Divorced' ? 'selected' : '';
                            }
                            ?>>Divorced</option>
                        </select>
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][6] ?> <span class="required"> *</span></label>
                        <input type="text" name="father_name" value="<?php
                        if (!empty($employee_info->father_name)) {
                            echo $employee_info->father_name;
                        }
                        ?>"  class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label"><?php echo $this->language->from_body()[12][7] ?><span class="required"> *</span></label>
                        <select name="nationality" class="form-control col-sm-5" >
                            <option value="" >Select Country...</option>
                            <?php foreach ($all_country as $v_country) : ?>
                                <option value="<?php echo $v_country->idCountry ?>" <?php
                                if (!empty($employee_info->country_id)) {
                                    echo $v_country->countryName == $employee_info->nationality ? 'selected' : '';
                                }
                                ?>><?php echo $v_country->countryName ?></option>
                                    <?php endforeach; ?>
                        </select> 
                    </div>
                    <div class="" id="nationality">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][8] ?></label>
                        <input type="text" name="passport_number" value="<?php
                        if (!empty($employee_info->passport_number)) {
                            echo $employee_info->passport_number;
                        }
                        ?>"  class="form-control">
                    </div>

                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-12">
                            <label for="field-1" class="control-label"><?php echo $this->language->from_body()[12][9] ?> <span class="required">*</span></label>
                            <div class="input-group">     
                                <input type="hidden" name="old_path" value="<?php
                                if (!empty($employee_info->photo_a_path)) {
                                    echo $employee_info->photo_a_path;
                                }
                                ?>">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                        <?php if (!empty($employee_info->photo)): ?>
                                            <img src="<?php echo base_url() . $employee_info->photo; ?>" >  
                                        <?php else: ?>
                                            <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">     
                                        <?php endif; ?>                                 
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                                        <input type="file" value="<?php if (!empty($employee_info)) echo base_url() . $employee_info->photo; ?>" name="photo" size="20" /><
                                    </div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new"><input type="file"  name="photo" size="20" /></span>
                                            <span class="fileinput-exists">Change</span>    
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                                <div id="valid_msg" style="color: #e11221"></div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>            
        </div> <!-- ************************ Personal Information Panel End ************************-->       
        <div class="col-sm-6"><!-- ************************ Contact Details Start******************************* -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4 class="panel-title"><?php echo $this->language->from_body()[12][16] ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][17] ?><span class="required"> *</span></label>
                        <textarea id="present" name="present_address" class="form-control" ><?php
                            if (!empty($employee_info->present_address)) {
                                echo $employee_info->present_address;
                            }
                            ?></textarea>
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[1][4] ?><span class="required"> *</span></label>
                        <input type="text" name="city" value="<?php
                        if (!empty($employee_info->city)) {
                            echo $employee_info->city;
                        }
                        ?>" class="form-control" >
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[1][5] ?><span class="required"> *</span></label>
                        <select name="country_id" class="form-control col-sm-5" >
                            <option value="" >Select Country...</option>
                            <?php foreach ($all_country as $v_country) : ?>
                                <option value="<?php echo $v_country->idCountry ?>" <?php
                                if (!empty($employee_info->country_id)) {
                                    echo $v_country->idCountry == $employee_info->country_id ? 'selected' : '';
                                }
                                ?>><?php echo $v_country->countryName ?></option>
                                    <?php endforeach; ?>
                        </select> 
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[1][8] ?><span class="required"> *</span></label>
                        <input type="text" name="mobile" value="<?php
                        if (!empty($employee_info->mobile)) {
                            echo $employee_info->mobile;
                        }
                        ?>" class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[1][7] ?></label>
                        <input type="text" name="phone" value="<?php
                        if (!empty($employee_info->phone)) {
                            echo $employee_info->phone;
                        }
                        ?>" class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[5][0] ?> <span class="required"> *</span></label>
                        <input type="email"  name="email" value="<?php
                        if (!empty($employee_info->email)) {
                            echo $employee_info->email;
                        }
                        ?>"  class="form-control">
                    </div>
                </div>
            </div>
        </div> <!-- ************************ Contact Details End ******************************* -->

        <div class="col-sm-6"><!-- ************************ Employee Documents Start ******************************* -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4 class="panel-title"><?php echo $this->language->from_body()[12][18] ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- CV Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][19] ?></label>
                        <input type="hidden" name="resume_path" value="<?php
                        if (!empty($employee_info->resume_path)) {
                            echo $employee_info->resume_path;
                        }
                        ?>">
                        <input type="hidden" name="document_id" value="<?php
                        if (!empty($employee_info->document_id)) {
                            echo $employee_info->document_id;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->resume)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="resume" value="<?php echo $employee_info->resume ?>">
                                        <input type="file" name="resume" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->resume_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="resume" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>

                    <!-- Offer Letter Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][20] ?></label>
                        <input type="hidden" name="offer_letter_path" value="<?php
                        if (!empty($employee_info->offer_letter_path)) {
                            echo $employee_info->offer_letter_path;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->offer_letter)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="offer_letter" value="<?php echo $employee_info->offer_letter ?>">
                                        <input type="file" name="offer_letter" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->offer_letter_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="offer_letter" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>

                    <!-- Joining Letter Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][21] ?></label>
                        <input type="hidden" name="joining_letter_path" value="<?php
                        if (!empty($employee_info->joining_letter_path)) {
                            echo $employee_info->joining_letter_path;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->joining_letter)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="joining_letter" value="<?php echo $employee_info->joining_letter ?>">
                                        <input type="file" name="joining_letter" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->offer_letter_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="joining_letter" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>

                    <!-- Contract Paper Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][22] ?></label>
                        <input type="hidden" name="contract_paper_path" value="<?php
                        if (!empty($employee_info->contract_paper_path)) {
                            echo $employee_info->contract_paper_path;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->contract_paper)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="contract_paper" value="<?php echo $employee_info->contract_paper ?>">
                                        <input type="file" name="contract_paper" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->offer_letter_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="contract_paper" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>

                    <!-- ID / Proff Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][23] ?></label>
                        <input type="hidden" name="id_proff_path" value="<?php
                        if (!empty($employee_info->id_proff_path)) {
                            echo $employee_info->id_proff_path;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->id_proff)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="id_proff" value="<?php echo $employee_info->id_proff ?>">
                                        <input type="file" name="id_proff" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->offer_letter_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="id_proff" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>

                    <!-- Medical Upload -->
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?php echo $this->language->from_body()[12][24] ?> </label>
                        <input type="hidden" name="other_document_path" value="<?php
                        if (!empty($employee_info->other_document_path)) {
                            echo $employee_info->other_document_path;
                        }
                        ?>">
                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php if (!empty($employee_info->other_document)): ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                        <span class="fileinput-exists" style="display: block">Change</span>
                                        <input type="hidden" name="other_document" value="<?php echo $employee_info->other_document ?>">
                                        <input type="file" name="other_document" >
                                    </span>                                    
                                    <span class="fileinput-filename"> <?php echo $employee_info->other_document_filename ?></span>                                          
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span>
                                        <span class="fileinput-exists" >Change</span>                                            
                                        <input type="file" name="other_document" >
                                    </span> 
                                    <span class="fileinput-filename"></span>                                        
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                <?php endif; ?>

                            </div>  
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ************************ Employee Documents Start ******************************* -->

        <!-- ************************      Bank Details Start******************************* -->
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4 class="panel-title"><?php echo $this->language->from_body()[12][25] ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][26] ?></label>
                        <input type="text" name="bank_name" value="<?php
                        if (!empty($employee_info->bank_name)) {
                            echo $employee_info->bank_name;
                        }
                        ?>" class="form-control" >
                        <input type="hidden" name="employee_bank_id" value="<?php
                        if (!empty($employee_info->employee_bank_id)) {
                            echo $employee_info->employee_bank_id;
                        }
                        ?>" class="form-control" >
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][27] ?></label>
                        <input type="text" name="branch_name" value="<?php
                        if (!empty($employee_info->branch_name)) {
                            echo $employee_info->branch_name;
                        }
                        ?>" class="form-control" >
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][28] ?></label>
                        <input type="text" name="account_name" value="<?php
                        if (!empty($employee_info->account_name)) {
                            echo $employee_info->account_name;
                        }
                        ?>" class="form-control">
                    </div>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][29] ?></label>
                        <input type="text"  name="account_number" value="<?php
                        if (!empty($employee_info->account_number)) {
                            echo $employee_info->account_number;
                        }
                        ?>" class="form-control">
                    </div>
                </div>
            </div>
        </div><!-- ************************ Bank Details End ******************************* -->        

        <div class="col-sm-6"><!-- ************************** official status column Start  ****************************-->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $this->language->from_body()[12][30] ?></h4>
                </div>
                <div class="panel-body">
                    <div class="">
                        <label for="field-1" class="control-label"><?php echo $this->language->from_body()[12][31] ?> <span class="required">*</span><small id="id_error_msg"></small></label>
                        <input type="text" name="employment_id" onchange="check_duplicate_emp_id(this.value)" value="<?php
                        if (!empty($employee_info->employment_id)) {
                            echo $employee_info->employment_id;
                        }
                        ?>" class="form-control" >
                    </div> 
                    <?php if (!empty($employee_info->status)) : ?>
                        <div class="">
                            <label class="control-label" >Status <span class="required">*</span></label>
                            <select name="status" class="form-control">
                                <option value="1" 
                                <?php
                                echo $employee_info->status == '1' ? 'selected' : '';
                                ?>><?php echo 'Active'; ?></option>                            
                                <option value="2" 
                                <?php
                                echo $employee_info->status == '2' ? 'selected' : '';
                                ?>><?php echo 'Inactive'; ?></option>                            

                            </select>
                        </div>                    
                    <?php endif; ?>
                    <div class="">
                        <label class="control-label" ><?php echo $this->language->from_body()[12][32] ?> <span class="required">*</span></label>
                        <select name="designations_id" class="form-control">                            
                            <option value="">Select Designations.....</option>
                            <?php if (!empty($all_department_info)): foreach ($all_department_info as $dept_name => $v_department_info) : ?>
                                    <?php if (!empty($v_department_info)): ?>
                                        <optgroup label="<?php echo $dept_name; ?>">
                                            <?php foreach ($v_department_info as $designation) : ?>
                                                <option value="<?php echo $designation->designations_id; ?>" 
                                                <?php
                                                if (!empty($employee_info->designations_id)) {
                                                    echo $designation->designations_id == $employee_info->designations_id ? 'selected' : '';
                                                }
                                                ?>><?php echo $designation->designations ?></option>                            
                                                    <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>                            
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>                    
                    <div class="">
                        <label for="field-1" class="control-label"><?php echo $this->language->from_body()[12][33] ?> <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker" name="joining_date" value="<?php
                            if (!empty($employee_info->joining_date)) {
                                echo $employee_info->joining_date;
                            }
                            ?>" data-format="yyyy/mm/dd" >
                            <div class="input-group-addon">
                                <a href="#"><i class="entypo-calendar"></i></a>
                            </div>
                        </div>
                    </div>                    
                </div>

            </div>
        </div><!-- ************************** official status column End  ****************************-->
        <div class="col-sm-6 margin pull-right">
            <button id="btn_emp" type="submit" class="btn btn-primary btn-block"><?php echo $this->language->from_body()[1][12] ?></button>
        </div>    
    </div>    
</form>