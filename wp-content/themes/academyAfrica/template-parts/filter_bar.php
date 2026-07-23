<?php
// get from $args
$sort_by = $args["sort_by"] ?? academyafrica_translate('Search By');
$sort = $args["sort"];
$filter_by = $args["filter_by"] ?? academyafrica_translate('Filter By');
$filter_options = $args["filter_options"] ?? [];
$sort_options = $args["sort_options"] ?? [];
?>

<aside class="filter-sidebar">
    <div class="sidebar" id="sidebar">
        <div class="sort">
            <p class="sort-by">
                <?php echo $sort_by ?>
            </p>
            <select name="sort" id="courses-sort" class="select" onchange="sortCourses(this)">
                <?php
                foreach ($sort_options as $key => $option) {
                    $selected = $sort == $key ? "selected" : "";
                ?>
                    <option <?php echo $selected ?> value="<?php echo $key ?>"><?php echo $option["name"] ?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <?php if (!empty($filter_options)) {
        ?>
            <div class="filter" id="side-filter-bar">
                <p class="filter-by">
                    <?php echo $filter_by ?>
                </p>
                <div class="filter-body">
                    <?php
                    if (!empty($filter_options)) {
                        foreach ($filter_options as $item) {
                            $title = $item["title"];
                            $options = $item["options"];
                    ?>
                            <div class="filter-item">
                                <p class="filter-by-title">
                                    <?php echo $title ?>
                                </p>
                                <?php
                                if (!empty($options)) {
                                ?>
                                    <ul class="filter-list">
                                        <?php
                                        foreach ($options as $options_index => $option) {
                                        ?>
                                            <?php if ($options_index >= 3) {
                                            ?>
                                                <li class="hidden">
                                                    <label class="mui-checkbox">
                                                        <input type="checkbox" onclick="filterSearchCourses(this, '<?php echo $item["name"] ?>', '<?php echo $option->name ?>')" value="<?php echo $option->id ?>" name="<?php echo $item["name"] . '-' . $option->name ?>">
                                                        <span class="checkmark"></span>
                                                        <?php echo $option->name ?>
                                                    </label>
                                                </li>
                                            <?php
                                            } else {
                                            ?>
                                                <li>
                                                    <label class="mui-checkbox">
                                                        <input type="checkbox" onclick="filterSearchCourses(this, '<?php echo $item["name"] ?>', '<?php echo $option->name ?>')" value="<?php echo $option->id ?>" name="<?php echo $item["name"] . '-' . $option->name ?>">
                                                        <span class="checkmark"></span>
                                                        <?php echo $option->name ?>
                                                    </label>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                    <?php if (count($options) > 3) {
                                    ?>
                                        <div class="show-more">
                                            <button class="show-more-btn">
                                                <?php echo esc_html(academyafrica_translate('Show More')); ?>
                                            </button>
                                        </div>
                                    <?php
                                    } ?>
                                <?php
                                }
                                ?>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>

        <?php
        } ?>
    </div>
</aside>


<div class="mobile-sidebar" id="mobile-filter">
    <div class="filter-modal" id="filter-modal">
        <div class="filter-options">
            <div id="mobile-filters" class="mobile-filter">
                <div class="filter-header">
                    <h4 class="filter-title">
                        <?php echo $filter_by ?>
                    </h4>
                    <div class="close">
                        <button class="buttons" id="close-filter-modal">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_11905_80119)">
                                    <path d="M8.0026 14.6654C11.6845 14.6654 14.6693 11.6806 14.6693 7.9987C14.6693 4.3168 11.6845 1.33203 8.0026 1.33203C4.32071 1.33203 1.33594 4.3168 1.33594 7.9987C1.33594 11.6806 4.32071 14.6654 8.0026 14.6654Z" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 6L6 10" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6 6L10 10" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_11905_80119">
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <?php echo esc_html(academyafrica_translate('Close')); ?>
                        </button>
                    </div>
                </div>
                <div class="filters">
                    <?php
                    if (!empty($filter_options)) {
                        foreach ($filter_options as $item) {
                            $title = $item["title"];
                            $options = $item["options"];

                    ?>
                            <div class="accordion-parent">
                                <button class="accordion"><?php echo $title ?></button>
                                <?php
                                if (!empty($options)) {
                                ?>
                                    <div class="panel">
                                        <ul>
                                            <?php
                                            foreach ($options as $option) {
                                            ?>
                                                <li>
                                                    <label class="mui-checkbox">
                                                        <input type="checkbox" onclick="filterSearchCourses(this, '<?php echo $item["name"] ?>', '<?php echo $option->name ?>')" value="<?php echo $option->id ?>" name="<?php echo $item["name"] . '-' . $option->name ?>">
                                                        <span class="checkmark"></span>
                                                        <?php echo $option->name ?>
                                                    </label>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                            </div>
                <?php
                                }
                            }
                        }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
