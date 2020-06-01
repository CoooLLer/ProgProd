<div class="row">
    <div class="col-12">
        <form method="post">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" aria-describedby="dateHelp"
                       placeholder="Enter date" value="<?= $data['topDate'] ?? '' ?>">
                <small id="dateHelp" class="form-text text-muted">Enter date for movies top</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?php foreach ($data['movies'] as $movieData): ?>
            <div class="movie-card">
                <div class="movie-header" style="background: url('<?=$movieData['movie']->getPicture()?>'); background-size: cover;">

                </div><!--movie-header-->
                <div class="movie-content">
                    <div class="movie-content-header">
                        <a href="#">
                            <h3 class="movie-title"><?=$movieData['movie']->getName()?></h3>
                        </a>
                    </div>
                    <div class="movie-info">
                        <div class="info-section">
                            <label>Year</label>
                            <span><?=$movieData['movie']->getYear()?></span>
                        </div><!--date,time-->
                        <div class="info-section">
                            <label>Rating</label>
                            <span><?=$movieData['rating']->getRating()?></span>
                        </div><!--screen-->
                        <div class="info-section">
                            <label>Voters</label>
                            <span><?=$movieData['rating']->getVotersCount()?></span>
                        </div><!--row-->
                    </div>
                </div><!--movie-content-->
            </div><!--movie-card-->
        <?php endforeach; ?>
    </div>
</div>

