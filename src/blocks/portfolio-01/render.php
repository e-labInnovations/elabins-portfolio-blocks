<?php

// $profile = wp_remote_get('https://raw.githubusercontent.com/e-labInnovations/e-labInnovations/refs/heads/master/portfolio-data.json');
// $githubStats = wp_remote_get('https://raw.githubusercontent.com/e-labInnovations/e-labInnovations/refs/heads/master/github-stats.json');

$sampleDataUrl = plugins_url('assets/sample-data/', __FILE__);
$sampleDataUrl = ELABINS_PORTFOLIO_BLOCKS_URL . "/assets/sample-data/";

$profile = wp_remote_get($sampleDataUrl . "portfolio-data.json");
$githubStats = wp_remote_get($sampleDataUrl . "github-stats.json");

if (is_wp_error($profile) || is_wp_error($githubStats)) {
  echo '<div class="error">Failed to load portfolio data. Please try again later.</div>';
  return;
}

$profileData = json_decode(wp_remote_retrieve_body($profile), true);
$githubStatsData = json_decode(wp_remote_retrieve_body($githubStats), true);
?>

<section class="elabins-portfolio-01">
  <!--- Schema Data --->
  <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Person",
      "name": "<?php echo $profileData['profile']['name']; ?>",
      "image": "<?php echo $profileData['profile']['profile_image']; ?>",
      "jobTitle": "<?php echo $profileData['profile']['title']; ?>",
      "url": "https://elabins.com/about-me/",
      "sameAs": [
        <?php foreach ($profileData['socialLinks'] as $link) : ?> "<?php echo $link['link']; ?>",
        <?php endforeach; ?>
      ]
    }
  </script>

  <!-- Hero Section -->
  <div class="hero-section" data-aos="fade-up">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <img class="profile-image" src="<?php echo $profileData['profile']['profile_image']; ?>"
        alt="<?php echo $profileData['profile']['name']; ?>" data-aos="zoom-in" />
      <div class="name-title">
        <h1 data-aos="fade-up" data-aos-delay="300"><?php echo $profileData['profile']['name']; ?></h1>
        <p class="typing-effect" data-aos="fade-up" data-aos-delay="500">
          <?php echo $profileData['profile']['title']; ?>
        </p>
      </div>
    </div>
  </div>

  <!-- About Section -->
  <div class="about-section">
    <div class="about-text" data-aos="fade-right" data-aos-delay="100">
      <h2>About Me</h2>
      <?php foreach ($profileData['profile']['about'] as $paragraph) : ?>
        <p><?php echo $paragraph; ?></p>
      <?php endforeach; ?>
    </div>
    <div class="about-card" data-aos="fade-left" data-aos-delay="300">
      <h2>Quick Facts</h2>
      <ul>
        <?php foreach ($profileData['profile']['facts'] as $fact) : ?>
          <li><i class="fas <?php echo $fact['icon']; ?>"></i> <?php echo $fact['content']; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Education Section -->
  <div class="education-section" data-aos="fade-up">
    <h2>Education</h2>
    <div class="education-grid">
      <?php foreach ($profileData['education'] as $edu) : ?>
        <div class="education-card" data-aos="fade-up">
          <div class="education-meta">
            <img src="<?php echo $edu['logo']; ?>" alt="<?php echo $edu['college']; ?>" />
            <div class="meta-info">
              <div class="duration">
                <i class="fas fa-calendar-alt"></i>
                <?php
                echo date('M Y', strtotime($edu['startDate'])) . ' - ' .
                  ($edu['endDate'] ? date('M Y', strtotime($edu['endDate'])) : 'Present');
                ?>
              </div>
              <div class="university">
                <i class="fas fa-university"></i>
                <?php echo $edu['university']; ?>
              </div>
            </div>
          </div>
          <div class="education-content">
            <h3 class="degree"><?php echo $edu['degree']; ?></h3>
            <p class="field"><?php echo $edu['fieldOfStudy']; ?></p>
            <p class="college"><?php echo $edu['college']; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Experience Section -->
  <div class="experience-section" data-aos="fade-up">
    <h2>Experience</h2>
    <div class="experience-grid">
      <?php foreach ($profileData['experience'] as $exp) : ?>
        <div class="experience-card" data-aos="fade-up">
          <div class="experience-meta">
            <img src="<?php echo $exp['company']['logo']; ?>" alt="<?php echo $exp['company']['name']; ?>" />
            <div class="meta-info">
              <div class="duration">
                <i class="fas fa-calendar-alt"></i>
                <?php
                echo date('M Y', strtotime($exp['startDate'])) . ' - ' .
                  ($exp['endDate'] ? date('M Y', strtotime($exp['endDate'])) : 'Present');
                ?>
              </div>
              <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo $exp['location']; ?> • <?php echo $exp['locationType']; ?>
              </div>
            </div>
          </div>
          <div class="experience-content">
            <h3 class="title"><?php echo $exp['title']; ?></h3>
            <a href="<?php echo $exp['company']['link']; ?>" target="_blank" class="company">
              <?php echo $exp['company']['name']; ?>
              <i class="fas fa-external-link-alt"></i>
            </a>
            <div class="type">
              <span class="badge"><?php echo $exp['employmentType']; ?></span>
            </div>
            <?php if ($exp['description']): ?>
              <p class="description"><?php echo $exp['description']; ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Projects Section -->
  <div class="projects-section" data-aos="fade-up">
    <h2>Projects</h2>
    <div class="projects-grid">
      <?php foreach ($profileData['projects'] as $project) : ?>
        <div class="project-card" data-aos="fade-up">
          <div class="project-header">
            <div class="header-content">
              <h3><?php echo $project['name']; ?></h3>
              <div class="duration">
                <i class="fas fa-calendar-alt"></i>
                <?php
                echo date('M Y', strtotime($project['startDate'])) . ' - ' .
                  ($project['endDate'] ? date('M Y', strtotime($project['endDate'])) : 'Present');
                ?>
              </div>
            </div>
            <div class="header-links">
              <?php if (!empty($project['links']['github'])): ?>
                <a href="<?php echo $project['links']['github']; ?>" target="_blank" class="project-link"
                  title="View on GitHub">
                  <i class="fab fa-github"></i>
                </a>
              <?php else: ?>
                <span class="project-link private-link" title="Private/Closed Source Project">
                  <i class="fas fa-lock"></i>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <div class="project-content">
            <p class="description"><?php echo $project['description']; ?></p>

            <?php if (!empty($project['collaborators'])): ?>
              <div class="collaborators">
                <h4>Collaborators</h4>
                <div class="collaborators-list">
                  <?php foreach ($project['collaborators'] as $collaborator): ?>
                    <div class="collaborator">
                      <?php if (!empty($collaborator['link'])): ?>
                        <a href="<?php echo $collaborator['link']; ?>" target="_blank" class="collaborator-name">
                          <?php echo $collaborator['name']; ?>
                          <i class="fas fa-external-link-alt"></i>
                        </a>
                      <?php else: ?>
                        <span class="collaborator-name"><?php echo $collaborator['name']; ?></span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="skills-list">
              <?php foreach ($project['skills'] as $skill): ?>
                <span class="skill-tag"><?php echo $skill; ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Skills Section -->
  <div class="skills-section" data-aos="fade-up">
    <h2>Tech Stack & Tools</h2>
    <div class="skills-grid">
      <?php foreach ($profileData['techStack'] as $tech): ?>
        <a href="<?php echo $tech['link']; ?>" target="_blank" class="skill-item" data-aos="zoom-in">
          <div class="skill-icon">
            <img src="<?php echo $tech['image']; ?>" alt="<?php echo $tech['name']; ?>" loading="lazy" />
          </div>
          <span class="skill-name">
            <?php echo $tech['name']; ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Honors & Awards Section -->
  <div class="awards-section" data-aos="fade-up">
    <h2>Honors & Awards</h2>
    <div class="awards-grid">
      <?php foreach ($profileData['honorsAndAwards'] as $award): ?>
        <div class="award-card" data-aos="fade-up">
          <div class="award-header">
            <?php if (!empty($award['image'])): ?>
              <img src="<?php echo $award['image']; ?>" alt="<?php echo $award['title']; ?>" />
            <?php else: ?>
              <div class="award-icon">
                <i class="fas fa-trophy"></i>
              </div>
            <?php endif; ?>
            <div class="award-date">
              <i class="fas fa-calendar"></i>
              <?php echo date('M Y', strtotime($award['issueDate'])); ?>
            </div>
          </div>

          <div class="award-content">
            <h3><?php echo $award['title']; ?></h3>
            <div class="award-issuer">
              <i class="fas fa-award"></i>
              <?php echo $award['issuer']; ?>
            </div>
            <p class="award-description"><?php echo $award['description']; ?></p>
            <?php if (!empty($award['link'])): ?>
              <a href="<?php echo $award['link']; ?>" target="_blank" class="award-link">
                Learn More
                <i class="fas fa-external-link-alt"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- GitHub Details Section -->
  <div class="github-section" data-aos="fade-up">
    <h2>GitHub Statistics</h2>
    <div class="github-grid">
      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-code-branch"></i>
        </div>
        <div class="github-content">
          <h3>Public Repositories</h3>
          <p class="github-stat"><?php echo $githubStatsData['user']['publicRepos']; ?></p>
        </div>
      </div>

      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="github-content">
          <h3>GitHub Followers</h3>
          <p class="github-stat"><?php echo $githubStatsData['user']['followers']; ?></p>
        </div>
      </div>

      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-star"></i>
        </div>
        <div class="github-content">
          <h3>Total Stars</h3>
          <p class="github-stat"><?php echo array_sum($githubStatsData['repoStarCount']); ?></p>
        </div>
      </div>

      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-code-commit"></i>
        </div>
        <div class="github-content">
          <h3>Total Commits</h3>
          <p class="github-stat"><?php echo array_sum($githubStatsData['quarterCommitCount']); ?></p>
        </div>
      </div>

      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-code"></i>
        </div>
        <div class="github-content">
          <h3>Languages Used</h3>
          <p class="github-stat"><?php echo count($githubStatsData['langRepoCount']); ?></p>
        </div>
      </div>

      <div class="github-card" data-aos="fade-up">
        <div class="github-icon">
          <i class="fas fa-file-code"></i>
        </div>
        <div class="github-content">
          <h3>Public Gists</h3>
          <p class="github-stat"><?php echo $githubStatsData['user']['publicGists']; ?></p>
        </div>
      </div>
    </div>

    <!-- Commit Activity Graph -->
    <?php
    // Sort and get last 12 quarters
    $quarters = array_slice(array_keys($githubStatsData['quarterCommitCount']), -12);
    $commitData = array_map(function ($quarter) use ($githubStatsData) {
      return $githubStatsData['quarterCommitCount'][$quarter];
    }, $quarters);
    ?>
    <div class="commit-activity" data-aos="fade-up"
      data-quarters="<?php echo htmlspecialchars(json_encode($quarters)); ?>"
      data-commits="<?php echo htmlspecialchars(json_encode($commitData)); ?>">
      <h3>Commit Activity</h3>
      <div class="chart-container">
        <canvas id="commitChart"></canvas>
      </div>
    </div>
  </div>
</section>