<?php

wp_enqueue_script('canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', [], ACADEMY_AFRICA_VERSION);
wp_enqueue_script('jsPDF', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', [], ACADEMY_AFRICA_VERSION);
wp_enqueue_script('html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', [], ACADEMY_AFRICA_VERSION);

$congratulations = academyafrica_translate('Congratulations');
$certificate_title = academyafrica_translate('CERTIFICATE OF');
$certificate_type = academyafrica_translate('COMPLETION');
$share_title = academyafrica_translate('Share the good news!');
$presented_to = academyafrica_translate('PRESENTED TO');
$certificate_description = academyafrica_translate('For completing a course on');
$course_id = $args["course_id"] ?? null;
$course = get_post($course_id);
$completion_date = get_the_date('Y-m-d', $course_id);
$course_title = get_the_title($course);
$certificate_course = get_the_title($course);
$course_link = get_permalink($course_id);
$company_name = "academy.Africa";
$user_id = get_current_user_id();
$certificate_link = $course_id ? learndash_get_course_certificate_link($args["course_id"], $user_id) : '';
// May be null when the course has no assigned certificate, or it was deleted
// or unpublished. Callers below must guard before using it (#46).
$cert_post = academyafrica_get_course_certificate_post($course_id);
$user = array(
    "first_name" => get_user_meta($user_id, 'first_name', true),
    "last_name" => get_user_meta($user_id, 'last_name', true),
);

$course_meta = get_post_meta($course_id, 'sfwd-courses', true);

$share_message_template = academyafrica_translate("🎉 Just completed the %s on academy.Africa!\n🚀 Ready to take on new challenges and apply what I've learned.\nCheck out the course 👉🏽.");
$share_message = sprintf($share_message_template, $course_title);


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
    'name' => "Tolulope Adeyemo",
    'role' => academyafrica_translate('Head of Academy'),
    'signature' => get_stylesheet_directory_uri() . '/assets/images/signature.png',
    'date' => date("d/m/Y")
);
global $shortcode_tags;
?>
<div class="course-completed">
    <h4 class="cfa-title">
        <?php echo $congratulations ?>
    </h4>
    <?php if ($cert_post) : ?>
        <div class="cert-pdf">
            <?php echo do_shortcode($cert_post->post_content) ?>
        </div>
    <?php endif; ?>
    <div class="content">
        <?php get_template_part('template-parts/certificate', 'template', array("academy_head" => $academy_head, "course" => array("date" => $completion_date, "name" => $certificate_course), "user" => $user)); ?>
        <div style="flex: 1; display: flex; justify-content: center;">
            <div class="share-section">
                <h4 class="title">
                    <?php echo $share_title ?>
                </h4>
                <div class="share" style="display: flex; justify-content: center;">
                    <?php get_template_part('template-parts/social_share', 'template', array('message' => $share_message)); ?>
                </div>
                <div style="display: flex; gap: 16px; justify-content: center; margin-top: 16px; flex-direction: column;">
                    <?php if (!empty($certificate_link)) : ?>
                    <a href="<?php echo esc_url($certificate_link) ?>" download>
                        <button class="button primary" id="download-certificate">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="Icon">
                                    <path id="Vector" d="M14.5 10.5V13.1667C14.5 13.5203 14.3595 13.8594 14.1095 14.1095C13.8594 14.3595 13.5203 14.5 13.1667 14.5H3.83333C3.47971 14.5 3.14057 14.3595 2.89052 14.1095C2.64048 13.8594 2.5 13.5203 2.5 13.1667V10.5" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path id="Vector_2" d="M5.16797 7.16797L8.5013 10.5013L11.8346 7.16797" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path id="Vector_3" d="M8.5 10.5V2.5" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                            </svg>
                            <?php echo esc_html(academyafrica_translate('Download')); ?>
                        </button>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo get_permalink($course_id) ?>">
                        <button class="button primary">
                            <?php echo esc_html(academyafrica_translate('View Course')); ?>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function convertHTMLtoPDF() {
            const {
                jsPDF
            } = window.jspdf;

            let doc = new jsPDF('l', 'mm', [210, 297]);
            doc.setFillColor(255, 255, 255);
            let pdfjs = document.getElementById('certificate');
            const width = doc.internal.pageSize.getWidth();
            const height = doc.internal.pageSize.getHeight();
            doc.html(pdfjs, {
                callback: function(doc) {
                    doc.save(`<?php echo $user['first_name'] . ' ' . $user['first_name'] ?> | <?php echo $certificate_course ?>.pdf`);
                },
                width: width,
                height,
                windowWidth: 891,
                html2canvas: {
                    scale: 0.954,
                    backgroundColor: "#ffffff"
                },
            });
        }
        
    </script>
</div>
