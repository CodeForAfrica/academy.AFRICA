<? $cfa_certificate_logo = get_stylesheet_directory_uri() . '/assets/images/cfa-certificate-logo.svg';
$cfa_certificate_logo_2 = get_stylesheet_directory_uri() . '/assets/images/cfa-certificate-logo-2.svg';
$academy_head_signature = get_stylesheet_directory_uri() . '/assets/images/signature.svg';
$academy_head = $args['academy_head'];
$mooc_logo_black = get_stylesheet_directory_uri() . '/assets/images/mooc-logo-black.svg';
// https://www.figma.com/design/0EnVajzsLy2eqmXUi79HnB/CfA---MOOC?node-id=13501-132650&t=PgDiEC6Alfdqk8GR-4
?>
<div class="certificate-preview">
<div class="certificate-preview-body">
<div class="divider-black"></div>
<div class="grid-container">
  <div class="grid-item">
    <p>CERTIFICATE OF</p>
    <p>
      <strong>
      COMPLETION
      </strong>
    </p>
    <div class="divider-blue"></div>
  </div>
 
  <div class="full-image">
    <img src="<? echo $mooc_logo_black?>" alt="mooc_logo_black" class="mooc-logo-black">
  </div>
</div>
<div>
  <div class="certificate-content">
    <div class="name">
      <p class="presented-to">
      PRESENTED TO
      </p>
      <p class="first-name">
      <?php echo $args['user']['first_name']; ?>
      </p>
      <p class="last-name">
      <?php echo $args['user']['last_name']; ?>
      </p>
    </div>
    <div class="course">
      <p></p>
      <p class="course-title">For completing a course on</p>
      <p class="course-name">
      <?php echo $args['course']['name']; ?>
      </p>
      <p class="date">
      <?php echo $args['course']['date']; ?>
      </p>
    </div>
  </div>
</div>
</div>
<div class="certificate-preview-footer">
  <div>
<img src="<? echo $cfa_certificate_logo?>" alt="cfa_certificate_logo" class="cfa-certificate-logo">
  </div>
  <div class="cfa-logo">
  <img src="<? echo $cfa_certificate_logo_2?>" alt="cfa_certificate_logo" class="cfa-certificate-logo">
  </div>
  <div class="signature">
  <img src="<? echo $academy_head_signature?>" alt="academy_head_signature" class="academy-head-signature">
  <p class="signee-name">
      <? echo $academy_head['name'] || "Tolulope Adeyemo" ?>
  </p>
  <p class="signee-role">
      <? echo $academy_head['role'] ?>
  </p>
  <p class="sign-date">
      <? echo $academy_head['date'] ?>
  </p>
  </div>
</div>
</div>
