<div class="w3-modal" id="add_year_modal">
	<div class="w3-modal-content w3-white rounded w3-card-16" style="width: 400px;">
		<div class="w3-padding-large rounded-top bg-info">
			Create new academic year <i class="fa fa-times w3-right pointer w3-hover-text-red" onclick="$('#add_year_modal').fadeOut();"></i>
		</div>
		<div class="w3-col m12 w3-padding w3-white rounded-bottom sign_form_wrapper">
			<form id="add_academic">
				<div id="aca_result"></div>
				<div class="single_form">
                    <input type="text" name="academic_year" class="form-control" placeholder="Academic year name.." required>
                    <i class="fa fa-i-cursor"></i>
                </div>
				<div class="single_form">
                    <input type="text" name="fees_year" class="form-control" placeholder="School fees.." required>
                    <i class="fa fa-money-bill-alt"></i>
                </div>
				<div class="single_form">
                    <textarea name="uniform" class="form-control" placeholder="School uniform.." required></textarea>
                    <i class="fa fa-female"></i>
                </div><br>
				<input type="hidden" name="create_academic_year" value="true">
				<center>
					<button class="btn btn-sm btn-info w3-padding-large">Create year</button>
				</center>
			</form>
		</div>
	</div>
</div>

<div class="w3-modal" id="edit_year_modal">
	<div class="w3-modal-content w3-white rounded w3-card-16" style="width: 400px;">
		<div class="w3-padding-large rounded-top bg-info">
			Edit academic year <i class="fa fa-times w3-right pointer w3-hover-text-red" onclick="$('#edit_year_modal').fadeOut();"></i>
		</div>
		<div class="w3-col m12 w3-padding w3-white rounded-bottom" id="edit_year_content">
			
		</div>
	</div>
</div>


<div class="w3-modal" id="add_student_modal" style="padding-top: 40px;">
	<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 350px;">
		<div class="w3-padding-large bg-info rounded-top w3-text-white">
			Add a Student <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#add_student_modal').fadeOut();"></i>
		</div>
		<div class="w3-padding-large">
			<form id="add_student_form">
				<div id="add_student_result"></div>
				<p>
					<input type="text" name="reg_number" class="form-control" placeholder="Enter reg number..." required>
				</p>
				<p>
					<input type="text" name="fullname" class="form-control" placeholder="Fullname.."  required>
				</p>
				<p><input type="text" name="village" class="form-control" placeholder="village..." ></p>
				<p><input type="text" name="church" class="form-control" placeholder="church.."  ></p>
				<p><input type="text" name="guardian" class="form-control" placeholder="guardian.."  ></p>

				<input type="hidden" name="add_student" value="true">
				
				<button class="btn btn-info btn-block" type="submit">Register</button>
				<input type="reset" name="" id="add_student_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>

<div class="w3-modal" id="edit_profile_modal" style="padding-top: 10px;">
	<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 400px;">
		<div class="w3-padding-large bg-info rounded-top w3-text-white">
			Edit Profile <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#edit_profile_modal').fadeOut();"></i>
		</div>
		<div class="w3-padding-large">
			<form id="edit_profile_form">
				<div id="edit_profile_result"></div>
				<div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="text" name="phone_edit" value="<?="$user_phone";?>" class="form-control" placeholder="Enter new phone number..." required>
	                    <i class="fa fa-phone"></i>
	                </div>
	            </div>
	            <div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="text" name="fullname_edit" class="form-control"  value="<?="$username";?>" placeholder="Enter new name.." required>
	                    <i class="far fa-user"></i>
	                </div>
	            </div>
	            <div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="password" name="old_password" class="form-control"   placeholder="Enter old password.." required>
	                    <i class="fa fa-key"></i>
	                </div>
	            </div>
	            <div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="password" name="new_password" class="form-control"   placeholder="Enter new password.." required>
	                    <i class="fa fa-key"></i>
	                </div>
	            </div>
				<br>
				<input type="hidden" name="edit_profile" value="true">
				<button class="btn btn-info btn-block w3-padding-large" type="submit">Update profile</button>
				<input type="reset" name="" id="edit_profile_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>

<div class="w3-modal" id="add_teacher_modal" style="padding-top: 40px;">
	<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 350px;">
		<div class="w3-padding-large bg-info rounded-top w3-text-white">
			Add a Teacher <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#add_teacher_modal').fadeOut();"></i>
		</div>
		<div class="w3-padding-large">
			<form id="add_teacher_form">
				<p>Fill the form to add a new teacher. The incoming teacher will have the default password of 1234</p>
				<div id="add_teacher_result"></div>
				<p><input type="text" name="phone_number" class="form-control" placeholder="Enter phone number..." required></p>
				<p><input type="text" name="fullname" class="form-control" placeholder="Fullname.."  required></p>
				<input type="hidden" name="add_teacher" value="true">
				<button class="btn btn-info btn-block" type="submit">Register</button>
				<input type="reset" name="" id="add_teacher_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>

<div class="w3-modal" id="edit_teacher_modal" style="padding-top: 50px;">
	<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 350px;">
		<div class="w3-padding-large bg-info rounded-top w3-text-white">
			<i class="fa fa-pen-alt"></i> Edit Teacher <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#edit_teacher_modal').fadeOut();"></i>
		</div>
		<div class="w3-padding-large">
			<form id="edit_teacher_form">
				<div id="edit_teacher_result"></div>
				<p><input type="text" name="phone_number_edit" class="form-control" placeholder="Enter phone number..." required></p>
				<p><input type="text" name="fullname_edit" class="form-control" placeholder="Fullname.." required></p>
				<input type="hidden" name="edit_teacher" id="edit_teacher" value="true">
				<button class="btn btn-info btn-block" type="submit">Save Changes</button>
				<input type="reset" name="" id="add_teacher_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>

<!--THE LOGOUT MODAL -->
<div class="w3-modal" id="logout_modal">
    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 300px;">
        <div class="w3-padding-large bg-info w3-text-white rounded-top">
            Confirm logout <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#logout_modal').slideUp();"></i>
        </div>
        <div class="w3-padding">
            Are you sure you want to logout??
            <p>&nbsp;</p>
            <div class="clearfix">
                <button class="btn btn-sm btn-danger" onclick="window.location = '../logout.php' ">Yes</button>
                <button class="btn btn-sm float-right" onclick="$('#logout_modal').slideUp();">No</button>
            </div>
        </div>
    </div>
</div>
<!--LOGOUT MODAL ENDS HERE -->



<!--THE CONFIRM DELETE TEACHER MODAL -->
<div class="w3-modal" id="confirm_delete">
    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 300px;">
        <div class="w3-padding-large bg-info w3-text-white rounded-top">
            <i class="fa fa-trash"></i> Confirm delete <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#confirm_delete').slideUp();"></i>
        </div>
        <div class="w3-padding">
            Are you sure you want to delete <b><font id="delete_teacher_name"></font></b>?
            <input type="hidden" name="del_teacher_id" id="del_teacher_id" value="">
            <p>&nbsp;</p>
            <div class="clearfix">
                <button class="btn btn-sm btn-danger" onclick="delete_the_teacher();">Yes</button>
                <button class="btn btn-sm float-right" onclick="$('#confirm_delete').slideUp();">No</button>
            </div>
        </div>
    </div>
</div>

<!--THE EDIT SUBJECT MODAL -->
<div class="w3-modal" id="edit_subject_modal">
    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 300px;">
        <div class="w3-padding-large bg-info w3-text-white rounded-top">
            <i class="fa fa-pen-alt"></i> Edit subject <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#edit_subject_modal').slideUp();"></i>
        </div>
        <div class="w3-padding">
        	<div id="edit_subject_result"></div>
            <form id="edit_subject_form">
            	<input type="hidden" name="edit_subject_id" id="edit_subject_id">
            	<p>
            		<input type="text" name="edit_subject_name" id="edit_subject_name" class="form-control" placeholder="Enter new name for subject">
            	</p>
            	<center>
            		<button class="btn btn-sm btn-info" type="submit">Update subject</button>
            	</center>
            </form>
        </div>
    </div>
</div>



<!--THE TOAST NOTIFICATION -->
<div class="w3-modal" id="toast_notification" style="padding-top: 20px;">
    <div class="w3-modal-content w3-card-16 w3-round-large w3-padding-large w3-text-white" id="toast_content" style="width: 300px;background: rgb(0,0,0,.7);">
        Hello New Toast Notification
    </div>
</div>

<!--Toast modal -->
<div class="w3-modal" id="toast_modal" style="padding-top: 30px;">
	<div class="w3-modal-content w3-padding-large rounded w3-text-white shadow" style="width: 300px;background: rgba(0,0,0,.56);" id="toast_modal_content">
		Hello World To Our Toast
	</div>
</div>

<div class="w3-modal" id="next_term_modal">
	<div class="w3-modal-content w3-card-16 rounded-2xl p-4" style="width: 350px;">
		<div class="w3-large pb-3">
			Edit Opening Day <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#next_term_modal').fadeOut();"></i>
		</div>
		<div>
			<form id="edit_next_term_form">
				<div id="edit_profile_result"></div>
				<p>
					Date 
					<input type="date" name="next_term_value" value="<?=$settings['next_term'];?>" class="form-control" placeholder="New date" required>
				</p>
				
				<button class="btn btn-info btn-block w3-round-jumbo" type="submit">Submit</button>
				<input type="reset" name="" id="edit_profile_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>