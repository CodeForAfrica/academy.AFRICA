<form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
    <div class="input">
        <input type="text" class="form-control" placeholder="Search Courses" aria-label="Search Courses" value="" name="s" id="s">
        <button class="button primary medium" type="submit" id="searchsubmit">
            <i class="fa-solid fa-magnifying-glass icon"></i>
        </button>
    </div>
    <button class="close-btn" id="search-close-btn" type="button">
        <i class="fa-solid fa-xmark" id="close"></i>
    </button>
</form>