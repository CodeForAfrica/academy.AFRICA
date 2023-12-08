<?php
// get from $args
$sort_by = $args["sort_by"];
$sort = $args["sort"];
$filter_by = $args["filter_by"];
$filter_options = $args["filter_options"];
$sort_options = $args["sort_options"];

?>

<aside class="filter-sidebar">
    <div class="sidebar" id="sidebar">
        <div class="sort">
            <p class="sort-by"> 
                <? echo $sort_by ?>
            </p>
            <select name="sort" id="courses-sort" class="select" onchange="sortCourses()">
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