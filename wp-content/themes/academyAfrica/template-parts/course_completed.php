<?php

$congratulations = "Congratulations";
$certificate_title = "CERTIFICATE OF";
$certificate_type = "COMPLETION";
$share_title = "Share the good news!";
$presented_to = "PRESENTED TO";
$certificate_description = "For completing the academy.AFRICA course";
$course_id = $args["course_id"] ?? null;
$course = get_post($course_id);
$certificate_course = get_the_title($course);
$company_name = "academy.Africa";
$user_id = get_current_user_id();
$certificate_link = learndash_get_course_certificate_link($args["course_id"], $user_id);
$certificate_id = learndash_get_setting($course_id, 'certificate');
$cert_post = get_post($certificate_id);
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
    'signature' => get_stylesheet_directory_uri() . '/assets/images/signature.png',
    'date' => date("d/m/Y")
);
global $shortcode_tags;
?>
<div class="course-completed">
    <h4 class="cfa-title">
        <? echo $congratulations ?>
    </h4>
    <div class="cert-pdf">
        <? echo do_shortcode($cert_post->post_content) ?>
    </div>
    <div class="content">
        <div class="certificate">
            <div class="certificate-content">
                <div class="certificate-content-header">
                    <hr />
                    <div class="certificate-header-details">
                        <p class="certificate-of">
                            <? echo $certificate_title ?>
                        </p>
                        <p class="certificate-type" style="font-weight: 700;">
                            <? echo $certificate_type ?>
                        </p>
                    </div>
                    <div class="certificate-header-logo">
                        <hr />
                        <img height="90px" width="90px" class="logo" alt="logo" style="background: transparent; margin-top: -80px; width: 110px; height: 110px"
                            src="<? echo get_stylesheet_directory_uri() . '/assets/images/mooc-logo-black.png' ?>" />
                    </div>

                </div>
                <div class="course-details" style="align-items: flex-start;">
                    <div class="student">
                        <p class="title" style="margin-bottom: 8px; font-size: 12px;">
                            <? echo $presented_to ?>
                        </p>
                        <p class="name first-name" style="margin-bottom: 12px; font-size: 36px;">
                            <? echo $user['first_name'] ?>
                        </p>
                        <p class="name bold-text" style="margin-bottom: 12px; font-size: 36px;">
                            <? echo $user['last_name'] ?>
                        </p>
                    </div>
                    <div class="course" style="max-width: 400px">
                        <p class="course-description title" style="text-align: right; font-size: 16px; margin-top: 8px;">
                            <? echo $certificate_description ?>
                        </p>
                        <p class="name bold-text" style="margin-bottom: 12px; font-size: 20px;">
                            <? echo $certificate_course ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="certificate-footer">
                <div class="company-details">
                    <div class="brand-details">
                        <img height="60px" width="60px" class="logo" alt="logo" style="background: transparent; width: 60px; height: 60px;"
                            src="<? echo get_stylesheet_directory_uri() . '/assets/images/mooc-logo-white.svg' ?>" />
                        <p class="company-name" style="line-height: 60px; font-size: 14px;">
                            <? echo $company_name ?>
                        </p>
                    </div>
                    <img class="artwork" height="60px" width="102px" alt="artwork" style="background: transparent; width: 80px; height: 40px;"
                        src="<? echo get_stylesheet_directory_uri() . '/assets/images/cfa_logo.svg' ?>" />
                </div>
                <div class="signature">
                    <img class="signature-img" alt="signature" alt="<? echo $academy_head['name'] ?>"
                        style="background: transparent; margin-bottom: 8px;" src="<? echo $academy_head['signature'] ?>" />
                    <p class="signee-name" style="margin-bottom: 8px; font-size: 12px;">
                        <? echo $academy_head['name'] ?>
                    </p>
                    <p class="signee-role" style="margin-bottom: 8px; font-size: 12px;">
                        <? echo $academy_head['role'] ?>
                    </p>
                    <p class="sign-date" style="font-size: 12px;">
                        <? echo $academy_head['date'] ?>
                    </p>
                </div>
            </div>
            <div class="certificate-site-name">
                <p style="color: #fff; margin: 0;">
                    www.academy.africa
                </p>
            </div>
        </div>
        <div class="share-section">
            <h4 class="title">
                <? echo $share_title ?>
            </h4>
            <div class="share" style="display: flex; justify-content: center;">
            <?php get_template_part('template-parts/social_share', 'template', array()); ?>
            </div>
            <a href="<? echo learndash_get_course_certificate_link($course_id) ?>" download>
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
    <script type="text/javascript">
        function convertHTMLtoPDF() {
            const { jsPDF } = window.jspdf;

            let doc = new jsPDF('l', 'mm', [210, 297]);
            doc.setFillColor(255, 255, 255);
            let pdfjs = document.getElementById('certificate');
            const width = doc.internal.pageSize.getWidth();
            const height = doc.internal.pageSize.getHeight();
            doc.html(pdfjs, {
                callback: function (doc) {
                    doc.save(`<? echo $user['first_name'] . ' ' . $user['first_name'] ?> | <? echo $certificate_course ?>.pdf`);
                },
                width: width,
                height,
                windowWidth: 891,
                html2canvas: { scale: 0.954, backgroundColor: "#ffffff" },
            });
        }            
    </script>
</div>
