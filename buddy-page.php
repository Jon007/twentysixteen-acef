<?php
/**
Template Name: buddy-page
*
* @package WordPress
* @subpackage Twenty_Sixteen_Acef
*
* drafts - not used - for custom group creation page
*/

$errors=array();     //add errors to here and output them later
$warnings=array();   
$maxstep=$stepid=0;  //max step will be set by validation and override requested step if higher
$teacher1id=$school1=$student1ids=0;
$teacher2ids=$school2=$student2ids=0;

function printArray($strings, $beforeitem, $afteritem)
{
    if (! $beforeitem){
        $beforeitem="<li>";
    }
    if (! $afteritem){
        $afteritem="</li>";
    }

    if (sizeof($strings)>0){
        foreach ($strings as $item) {
            echo($beforeitem . $item . $afteritem);
        }
    }
    
}
/*
 * Initialise and validate inputs as follows:
 * Teacher1 and School1 are set from current user, other values from parameters
 * u=student1id
 * s=school2
 * t=teacher2id
 * v=student2id
 * p=current step or page
 * 
 * Steps will be defined as:
 * 0-select student from your school
 * 1-select other school and supervisor teacher  (a modified search form??)
 * 2-select other school students [optional]
 * 3-create group and redirect to group
 * Steps 1 and 2 will be denied if previous steps not completed
 * 
 * Step 1 could have three buttons for example:
 * - Add teacher and select another teacher
 * - Add teacher and select student
 * - Add teacher and create group
 * 
 */
function init()
{
    global $errors, $warnings, $maxstep, $stepid, $teacher1id, $school1, $student1ids, 
        $teacher2ids, $school2, $student2ids;
    
    $teacher1id=get_current_user_id();
    if (! $teacher1id) {
        $errors[]=__('This function is only available to logged on users.', 'acef');
        return false;
    }
    if (bp_get_member_type($teacher1id) != 'teacher') {
        $errors[]=__('This function is only available to Teachers.', 'acef');
        return false;
    }
    $school1=xprofile_get_field_data('School', $teacher1id, 'comma');
    if (! $school1) {
        $errors[]=__('This function is only available to Teachers with a School.', 'acef');
        return false;
    }
    
    $school2=(isset($_GET['s'])) ? urldecode($_GET['s']) : false;
    $teacher2ids=(isset($_GET['t'])) ? array_map('intval',explode(',', $_GET['t'])): false;
    if (is_array($teacher2ids)){
        foreach ( $teacher2ids as $key => $value ) {
            if (bp_get_member_type($value) != 'teacher') {
                $warnings[]=$value . ' ' . bp_core_get_username($value) . 
                    __(' is not a teacher and was removed from teachers list.', 'acef');
                unset($teacher2ids[$key]);
            }else{
                $teacher2school=xprofile_get_field_data('School', $value, 'comma');
                if ($school2!=$teacher2school){
                    $warnings[]=$value . ' ' . bp_core_get_username($value) . ' ' . 
                        __('is a teacher at', 'acef') . ' "' . $teacher2school . '" ' .
                        __('and so was removed from teachers list for', 'acef') . ' ' . $school2;
                    unset($teacher2ids[$key]);
                }
            }
        }
        if (sizeof($teacher2ids)==0){
            $maxstep=1;
        } else {
            $maxstep=2;
        }
    } else {
        $maxstep=1;
    }
     
    
    $student1ids=(isset($_GET['u'])) ? array_map('intval',explode(',', $_GET['u'])) : false;
    if (! $student1ids ){
        $maxstep=0;
    } else {
        foreach ( $student1ids as $key => $value ) {
            if (bp_get_member_type($value) != 'student') {
                $warnings[]=$value . ' ' . bp_core_get_username($value) . 
                    __(' is not a student and was removed from students list.', 'acef');
                unset($student1ids[$key]);
            }else{
                $student1school=xprofile_get_field_data('School', $value, 'comma');
                if ($school1!=$student1school){
                    $warnings[]=$value . ' ' . bp_core_get_username($value) . ' ' . 
                        __('is a student at', 'acef') . ' "' . $student1school . '" ' .
                        __('and so was removed from students list for', 'acef') . ' ' . $school1;
                    unset($student1ids[$key]);
                }
            }
        }
        if (sizeof($student1ids)==0){
            $maxstep=0;
        }
    }
   
    $student2ids=(isset($_GET['v'])) ? array_map('intval',explode(',', $_GET['v'])): false;
    $stepid = (isset($_GET['p'])) ? intval($_GET['p']): false;
    if ($stepid>$maxstep){
        $stepid=$maxstep;
    }
    return true;
}

$valid=init();


//if on third step, create group
if ($stepid==3){
    $group_args = array(
        'creator_id' => 0, // this should be replaced with a user ID of a user that creates group
        'name' => jmbp_makeBuddyGroupTitle($student1ids + $student2ids),
        'description' => 'group description',
        'status' => 'public', // public, private or hidden
    );
    $group_id = groups_create_group($group_args);

}
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the page content template.
			get_template_part( 'template-parts/content', 'page' );

            // If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

			// End of the loop.
		endwhile;


        
        
      printArray($errors, '', '');
      printArray($warnings, '', '');        
		?>
Teacher1: <?php echo($teacher1id);?><br />
School1:  <?php echo($school1);?><br />
Student1: <?php if ($student1ids && is_array($student1ids)){echo(implode(',', $student1ids));}?><br />
Teacher2: <?php if ($teacher2ids && is_array($teacher2ids)) { echo(implode(',', $teacher2ids)); }?><br />
School2:  <?php echo($school2);?><br />
Student2: <?php if ($student2ids && is_array($student2ids)) { echo(implode(',',$student2ids));} ?><br />
Step: <?php echo($stepid);?><br />
MaxStep: <?php echo($maxstep);?><br />
	</main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
