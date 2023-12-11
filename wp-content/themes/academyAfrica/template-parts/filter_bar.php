<?php
// get from $args
$sort_by = $args["sort_by"] ?? "Search By";
$sort = $args["sort"];
$filter_by = $args["filter_by"] ?? "Filter By";
$filter_options = $args["filter_options"];
$sort_options = $args["sort_options"];

?>

<aside class="filter-sidebar">
    <div class="sidebar" id="sidebar">
        <div class="sort">
            <p class="sort-by">
                <? echo $sort_by ?>
            </p>
            <select name="sort" id="courses-sort" class="select" onchange="sortCourses(this)">
                <?
                foreach ($sort_options as $key => $option) {
                    $selected = $sort == $key ? "selected" : "";
                ?>
                    <option <? echo $selected ?> value="<? echo $key ?>"><? echo $option["name"] ?></option>
                <?
                }
                ?>
            </select>
        </div>
        <div class="filter" id="side-filter-bar">
            <p class="filter-by">
                <? echo $filter_by ?>
            </p>
            <div class="filter-body">
                <?
                if (!empty($filter_options)) {
                    foreach ($filter_options as $item) {
                        $title = $item["title"];
                        $options = $item["options"];
                ?>
                        <div class="filter-item">
                            <p class="filter-by-title">
                                <? echo $title ?>
                            </p>
                            <?
                            if (!empty($options)) {
                            ?>
                                <ul class="filter-list">
                                    <?
                                    foreach ($options as $options_index => $option) {
                                    ?>
                                        <? if ($options_index >= 3) {
                                        ?>
                                            <li class="hidden">
                                                <label class="mui-checkbox">
                                                    <input type="checkbox" onclick="filterSearchCourses(this, '<? echo $item["name"] ?>', '<? echo $option->name ?>')" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                    <span class="checkmark"></span>
                                                    <? echo $option->name ?>
                                                </label>
                                            </li>
                                        <?
                                        } else {
                                        ?>
                                            <li>
                                                <label class="mui-checkbox">
                                                    <input type="checkbox" onclick="filterSearchCourses(this, '<? echo $item["name"] ?>', '<? echo $option->name ?>')" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                    <span class="checkmark"></span>
                                                    <? echo $option->name ?>
                                                </label>
                                            </li>
                                        <?
                                        }
                                        ?>
                                    <?
                                    }
                                    ?>
                                </ul>
                                <? if (count($options) > 3) {
                                ?>
                                    <div class="show-more">
                                        <button class="show-more-btn">
                                            Show More
                                        </button>
                                    </div>
                                <?
                                } ?>
                            <?
                            }
                            ?>
                        </div>
                <?
                    }
                }
                ?>
            </div>
        </div>
    </div>
</aside>

<div class="mobile-sidebar" id="mobile-filter">
    <div class="filter-modal d-none" id="filter-modal">
        <div class="filter-options">
            <div id="mobile-filters" class="mobile-filter"> 
                <div class="filter-header">
                    <h4 class="filter-title">
                        <? echo $filter_by ?>
                    </h4>
                    <div class="close">
                        <button onclick="closeFilters()" class="buttons" id="close-filter-modal">
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
                            Close
                        </button>
                    </div>
                </div>
                <div class="filters">
                    <?
                    if (!empty($filter_options)) {
                        foreach ($filter_options as $item) {
                            $title = $item["title"];
                            $options = $item["options"];

                    ?>
                            <div class="accordion-parent">
                                <button class="accordion"><? echo $title ?></button>
                                <?
                                if (!empty($options)) {
                                ?>
                                    <div class="panel">
                                        <ul>
                                            <?
                                            foreach ($options as $option) {
                                            ?>
                                                <li>
                                                    <label class="mui-checkbox">
                                                        <input type="checkbox" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                        <span class="checkmark"></span>
                                                        <? echo $option->name ?>
                                                    </label>
                                                </li>
                                            <?
                                            }
                                            ?>
                                        </ul>
                                    </div>
                            </div>
                <?
                                }
                            }
                        }
                ?>
                </div>
                <div class="footer">
                    <hr class="divider">
                    <div class="footer-buttons">
                        <button class="button primary medium apply" onclick="applyFilters()">
                            Apply
                        </button>
                        <button class="clear" onclick="clearFilters()">
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
                            Clear all filters
                        </button>
                    </div>
                </div>
                <div class="mobile-close">
                    <button onclick="closeFilters()" class="buttons" id="close-filter-modal">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_11905_80119)">
                                <path d="M8.0026 14.6654C11.6845 14.6654 14.6693 11.6806 14.6693 7.9987C14.6693 4.3168 11.6845 1.33203 8.0026 1.33203C4.32071 1.33203 1.33594 4.3168 1.33594 7.9987C1.33594 11.6806 4.32071 14.6654 8.0026 14.6654Z" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10 6L6 10" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6 6L10 10" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_11905_80119">
                                    <rect width="16" height="16" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>