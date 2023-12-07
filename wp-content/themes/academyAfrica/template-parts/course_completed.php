<?php
$congratulations = "Congratulations";
$certificate_title = "CERTIFICATE OF";
$certificate_type = "COMPLETION";
$share_title = "Share the good news!";
$presented_to = "PRESENTED TO";
$certificate_description = "For completing the academy.AFRICA course";
$course = get_post($course_id);
$certificate_course = get_the_title($course);
$company_name = "academy.Africa";
$user_id = get_current_user_id();
$certificate_link = learndash_get_course_certificate_link($args["course_id"], $user_id);
$user = array(
    "first_name" => get_user_meta($user_id, 'first_name', true),
    "last_name" => get_user_meta($user_id, 'last_name', true),
);

$social_media_links = [
    [
        'link' => [
            'url' => 'https://www.facebook.com/CodeForAfrica',
        ],
        'type' => 'facebook',
    ],
    [
        'link' => [
            'url' => 'https://twitter.com/Code4Africa',
        ],
        'type' => 'twitter',
    ],
    [
        'link' => [
            'url' => 'https://www.instagram.com/code4africa/',
        ],
        'type' => 'instagram',
    ],
    [
        'link' => [
            'url' => 'https://www.linkedin.com/company/code-for-africa/',
        ],
        'type' => 'linkedin',
    ],
];

$academy_head = array(
    'name' => $settings['academy_head_name'],
    'role' => 'Head of Academy',
    'signature' => get_stylesheet_directory_uri().'/assets/images/signature.png',
    'date' => date("d/m/Y")
);
?>
<script>
    console.log(<? echo json_encode($args) ?>)
</script>
<div class="course-completed">
    <h4 class="cfa-title">
        <? echo $congratulations ?>
    </h4>
    <div class="content">
        <div class="certificate">
            <div class="certificate-content">
                <div class="certificate-content-header">
                    <hr />
                    <div class="certificate-header-details">
                        <p class="certificate-of">
                            <? echo $certificate_title ?>
                        </p>
                        <p class="certificate-type">
                            <? echo $certificate_type ?>
                        </p>
                    </div>
                    <div class="certificate-header-logo">
                        <hr />
                        <img class="logo" alt="logo"
                            src="<? echo get_stylesheet_directory_uri().'/assets/images/mooc-logo-black.svg' ?>" />
                    </div>

                </div>
                <div class="course-details">
                    <div class="student">
                        <p class="title">
                            <? echo $presented_to ?>
                        </p>
                        <p class="name first-name">
                            <? echo $user['first_name'] ?>
                        </p>
                        <p class="name bold-text">
                            <? echo $user['last_name'] ?>
                        </p>
                    </div>
                    <div class="course">
                        <p class="course-description title">
                            <? echo $certificate_description ?>
                        </p>
                        <p class="name bold-text">
                            <? echo $certificate_course ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="certificate-footer">
                <div class="company-details">
                    <div class="brand-details">
                        <img height="30px" width="30px" class="logo" alt="logo"
                            src="<? echo get_stylesheet_directory_uri().'/assets/images/mooc-logo-white.svg' ?>" />
                        <p class="company-name">
                            <? echo $company_name ?>
                        </p>
                    </div>
                    <img class="artwork" height="30px" width="52px" alt="artwork"
                        src="<? echo get_stylesheet_directory_uri().'/assets/images/cfa_logo.svg' ?>" />
                </div>
                <div class="signature">
                    <img class="signature-img" alt="signature" alt="<? echo $academy_head['name'] ?>"
                        src="<? echo $academy_head['signature'] ?>" />
                    <p class="signee-name">
                        <? echo $academy_head['name'] ?>
                    </p>
                    <p class="signee-role">
                        <? echo $academy_head['role'] ?>
                    </p>
                    <p class="sign-date">
                        <? echo $academy_head['date'] ?>
                    </p>
                </div>
            </div>
            <div class="certificate-site-name">
                <p>
                    www.academy.africa
                </p>
            </div>
        </div>
        <div class="share-section">
            <h4 class="title">
                <? echo $share_title ?>
            </h4>
            <div class="share">
                <div class="social-icons">
                    <?
                    if(!empty($social_media_links)) {
                        foreach($social_media_links as $item) {
                            $link = esc_url($item['link']['url']);
                            $type = esc_html($item['type']);
                            $icon = get_stylesheet_directory_uri().('/assets/images/icons/Type='.$type.', Size=24, Color=Black.svg');
                            $image = "<img class='icon-image' src='".$icon."' alt='".$type."' />";
                            echo '<a style="color: #000" href="'.$link.'" class="icon">'.$image.'</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <a href="<? echo $certificate_link ?>" download>
                <button class="button primary">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Icon">
                            <path id="Vector"
                                d="M14.5 10.5V13.1667C14.5 13.5203 14.3595 13.8594 14.1095 14.1095C13.8594 14.3595 13.5203 14.5 13.1667 14.5H3.83333C3.47971 14.5 3.14057 14.3595 2.89052 14.1095C2.64048 13.8594 2.5 13.5203 2.5 13.1667V10.5"
                                stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path id="Vector_2" d="M5.16797 7.16797L8.5013 10.5013L11.8346 7.16797" stroke="#EFF0FD"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path id="Vector_3" d="M8.5 10.5V2.5" stroke="#EFF0FD" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                    </svg>
                    Download
                </button>
            </a>
        </div>
    </div>
</div>