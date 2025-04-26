<?php

// $profile = wp_remote_get('https://raw.githubusercontent.com/e-labInnovations/e-labInnovations/refs/heads/master/portfolio-data.json');
// $githubStats = wp_remote_get('https://raw.githubusercontent.com/e-labInnovations/e-labInnovations/refs/heads/master/github-stats.json');
// $profile = wp_remote_get('http://localhost:8881/wp-content/plugins/elabins-portfolio-blocks/assets/sample-data/portfolio-data.json');
// $githubStats = wp_remote_get('http://localhost:8881/wp-content/plugins/elabins-portfolio-blocks/assets/sample-data/github-stats.json');

if (!isset($attributes['portfolioJsonUrl']) || !isset($attributes['githubStatsJsonUrl'])) {
  echo '<div class="elabins-portfolio-error"><i class="fas fa-exclamation-triangle"></i> Please configure the portfolio and GitHub stats URLs in the block settings.</div>';
  return;
}

$portfolioJsonUrl = $attributes['portfolioJsonUrl'];
$githubStatsJsonUrl = $attributes['githubStatsJsonUrl'];

$profile = wp_remote_get($portfolioJsonUrl);
$githubStats = wp_remote_get($githubStatsJsonUrl);

if (is_wp_error($profile) || is_wp_error($githubStats)) {
  echo '<div class="elabins-portfolio-error"><i class="fas fa-exclamation-triangle"></i> Failed to load portfolio data. Please verify the URLs and try again.</div>';
  return;
}

$profileData = json_decode(wp_remote_retrieve_body($profile), true);
$githubStatsData = json_decode(wp_remote_retrieve_body($githubStats), true);

if (!$profileData || !$githubStatsData) {
  echo '<div class="elabins-portfolio-error"><i class="fas fa-exclamation-triangle"></i> Invalid JSON data. Please check the data format.</div>';
  return;
}
?>

<!--- Schema Data --->
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Person",
  "name": "<?php echo htmlspecialchars($profileData['profile']['name'], ENT_QUOTES, 'UTF-8'); ?>",
  "image": "<?php echo htmlspecialchars($profileData['profile']['profile_image'], ENT_QUOTES, 'UTF-8'); ?>",
  "jobTitle": "<?php echo htmlspecialchars($profileData['profile']['title'], ENT_QUOTES, 'UTF-8'); ?>",
  "url": "https://elabins.com/about-me/",
  "description": "<?php echo htmlspecialchars(strip_tags($profileData['profile']['about'][0]), ENT_QUOTES, 'UTF-8'); ?>",
  "sameAs": [
    <?php
      $uniqueLinks = array_unique(array_column($profileData['socialLinks'], 'link'));
      foreach ($uniqueLinks as $index => $link):
      ?> "<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>"
    <?php echo $index < count($uniqueLinks) - 1 ? ',' : ''; ?>
    <?php endforeach; ?>
  ],
  "alumniOf": [
    <?php
      $uniqueEducation = array_unique($profileData['education'], SORT_REGULAR);
      foreach ($uniqueEducation as $index => $edu):
      ?> {
      "@type": "EducationalOrganization",
      "name": "<?php echo htmlspecialchars($edu['college'], ENT_QUOTES, 'UTF-8'); ?>",
      "url": "<?php echo htmlspecialchars($edu['logo'], ENT_QUOTES, 'UTF-8'); ?>"
    }
    <?php echo $index < count($uniqueEducation) - 1 ? ',' : ''; ?>
    <?php endforeach; ?>
  ],
  "worksFor": [
    <?php
      $uniqueExperience = array_unique($profileData['experience'], SORT_REGULAR);
      foreach ($uniqueExperience as $index => $exp):
      ?> {
      "@type": "Organization",
      "name": "<?php echo htmlspecialchars($exp['company']['name'], ENT_QUOTES, 'UTF-8'); ?>",
      "url": "<?php echo htmlspecialchars($exp['company']['link'], ENT_QUOTES, 'UTF-8'); ?>",
      "logo": "<?php echo htmlspecialchars($exp['company']['logo'], ENT_QUOTES, 'UTF-8'); ?>"
    }
    <?php echo $index < count($uniqueExperience) - 1 ? ',' : ''; ?>
    <?php endforeach; ?>
  ],
  "award": [
    <?php
      $uniqueAwards = array_unique($profileData['honorsAndAwards'], SORT_REGULAR);
      foreach ($uniqueAwards as $index => $award):
      ?> {
      "@type": "Award",
      "name": "<?php echo htmlspecialchars($award['title'], ENT_QUOTES, 'UTF-8'); ?>",
      "description": "<?php echo htmlspecialchars($award['description'], ENT_QUOTES, 'UTF-8'); ?>",
      "issuer": {
        "@type": "Organization",
        "name": "<?php echo htmlspecialchars($award['issuer'], ENT_QUOTES, 'UTF-8'); ?>"
      },
      "dateAwarded": "<?php echo htmlspecialchars($award['issueDate'], ENT_QUOTES, 'UTF-8'); ?>"
    }
    <?php echo $index < count($uniqueAwards) - 1 ? ',' : ''; ?>
    <?php endforeach; ?>
  ],
  "knowsAbout": [
    <?php
      $uniqueSkills = array_unique(array_column($profileData['techStack'], 'name'));
      $skills = array_map(function ($skill) {
        return '"' . htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') . '"';
      }, $uniqueSkills);
      echo implode(', ', $skills);
      ?>
  ],
  "hasOccupation": {
    "@type": "Occupation",
    "name": "<?php echo htmlspecialchars($profileData['profile']['title'], ENT_QUOTES, 'UTF-8'); ?>",
    "skills": [
      <?php echo implode(', ', $skills); ?>
    ]
  },
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      <?php
        $topProjects = array_slice($profileData['projects'], 0, 5);
        foreach ($topProjects as $index => $project):
        ?> {
        "@type": "SoftwareSourceCode",
        "name": "<?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?>",
        "description": "<?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?>",
        "programmingLanguage": [
          <?php
              $languages = array_map(function ($lang) {
                return '"' . htmlspecialchars(trim($lang), ENT_QUOTES, 'UTF-8') . '"';
              }, array_unique($project['skills']));
              echo implode(', ', $languages);
              ?>
        ],
        <?php if (!empty($project['links']['github'])): ?> "codeRepository": "<?php echo htmlspecialchars($project['links']['github'], ENT_QUOTES, 'UTF-8'); ?>",
        <?php endif; ?> "dateCreated": "<?php echo htmlspecialchars($project['startDate'], ENT_QUOTES, 'UTF-8'); ?>",
        "dateModified": "<?php echo htmlspecialchars($project['endDate'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
      }
      <?php echo $index < count($topProjects) - 1 ? ',' : ''; ?>
      <?php endforeach; ?>
    ]
  }
}
</script>

<section class="elabins-portfolio-01" itemscope itemtype="http://schema.org/Person">
  <!-- Hero Section -->
  <div class="hero-section" data-aos="fade-up" role="banner">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="hero-content">
      <img class="profile-image" src="<?php echo $profileData['profile']['profile_image']; ?>"
        alt="<?php echo $profileData['profile']['name']; ?>'s profile photo" data-aos="zoom-in" itemprop="image" />
      <div class="name-title">
        <h1 data-aos="fade-up" data-aos-delay="300" itemprop="name"><?php echo $profileData['profile']['name']; ?></h1>
        <div class="typewriter">
          <h2 itemprop="jobTitle">
            <?php echo $profileData['profile']['title']; ?>
          </h2>
        </div>
      </div>
    </div>
  </div>

  <!-- About Section -->
  <div class="about-section" role="region" aria-label="About Me">
    <div class="about-text" data-aos="fade-right" data-aos-delay="100">
      <h2>About Me</h2>
      <?php foreach ($profileData['profile']['about'] as $paragraph) : ?>
      <p itemprop="description"><?php echo $paragraph; ?></p>
      <?php endforeach; ?>
    </div>
    <div class="about-card" data-aos="fade-left" data-aos-delay="300">
      <h2>Quick Facts</h2>
      <ul role="list">
        <?php foreach ($profileData['profile']['facts'] as $fact) : ?>
        <li><i class="<?php echo $fact['icon']; ?>" aria-hidden="true"></i> <?php echo $fact['content']; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Education Section -->
  <div class="education-section" data-aos="fade-up" role="region" aria-label="Education">
    <h2>Education</h2>
    <div class="education-grid">
      <?php foreach ($profileData['education'] as $edu) : ?>
      <div class="education-card" data-aos="fade-up" itemprop="alumniOf" itemscope
        itemtype="http://schema.org/EducationalOrganization">
        <div class="education-meta">
          <img src="<?php echo $edu['logo']; ?>" alt="<?php echo $edu['college']; ?> logo" itemprop="logo" />
          <div class="meta-info">
            <div class="duration">
              <i class="fas fa-calendar-alt" aria-hidden="true"></i>
              <time datetime="<?php echo $edu['startDate']; ?>">
                <?php
                  echo date('M Y', strtotime($edu['startDate'])) . ' - ' .
                    ($edu['endDate'] ? date('M Y', strtotime($edu['endDate'])) : 'Present');
                  ?>
              </time>
            </div>
            <div class="university">
              <i class="fas fa-university" aria-hidden="true"></i>
              <span itemprop="name"><?php echo $edu['university']; ?></span>
            </div>
          </div>
        </div>
        <div class="education-content">
          <h3 class="degree" itemprop="award"><?php echo $edu['degree']; ?></h3>
          <p class="field" itemprop="studyField"><?php echo $edu['fieldOfStudy']; ?></p>
          <p class="college" itemprop="name"><?php echo $edu['college']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Experience Section -->
  <div class="experience-section" data-aos="fade-up" role="region" aria-label="Work Experience">
    <h2>Experience</h2>
    <div class="experience-grid">
      <?php foreach ($profileData['experience'] as $exp) : ?>
      <div class="experience-card" data-aos="fade-up" itemprop="workExperience" itemscope
        itemtype="http://schema.org/WorkExperience">
        <div class="experience-meta">
          <img src="<?php echo $exp['company']['logo']; ?>" alt="<?php echo $exp['company']['name']; ?> logo" />
          <div class="meta-info">
            <div class="duration">
              <i class="fas fa-calendar-alt" aria-hidden="true"></i>
              <time datetime="<?php echo $exp['startDate']; ?>">
                <?php
                  echo date('M Y', strtotime($exp['startDate'])) . ' - ' .
                    ($exp['endDate'] ? date('M Y', strtotime($exp['endDate'])) : 'Present');
                  ?>
              </time>
            </div>
            <div class="location">
              <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
              <span itemprop="location"><?php echo $exp['location']; ?></span> •
              <span itemprop="employmentType"><?php echo $exp['locationType']; ?></span>
            </div>
          </div>
        </div>
        <div class="experience-content">
          <h3 class="title" itemprop="jobTitle"><?php echo $exp['title']; ?></h3>
          <a href="<?php echo $exp['company']['link']; ?>" target="_blank" class="company" itemprop="worksFor" itemscope
            itemtype="http://schema.org/Organization">
            <span itemprop="name"><?php echo $exp['company']['name']; ?></span>
            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
          </a>
          <div class="type">
            <span class="badge" itemprop="employmentType"><?php echo $exp['employmentType']; ?></span>
          </div>
          <?php if ($exp['description']): ?>
          <p class="description" itemprop="description"><?php echo $exp['description']; ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Projects Section -->
  <div class="projects-section" data-aos="fade-up" role="region" aria-label="Projects"
    data-projects='<?php echo htmlspecialchars(json_encode($profileData['projects']), ENT_QUOTES, 'UTF-8'); ?>'>
    <h2>Projects</h2>
    <div class="projects-grid">
      <?php foreach ($profileData['projects'] as $projectIndex => $project) : ?>
      <div class="project-card" data-aos="fade-up" itemprop="workExample" itemscope
        itemtype="http://schema.org/SoftwareApplication">
        <div class="project-header">
          <div class="header-content">
            <h3 itemprop="name"><?php echo $project['name']; ?></h3>
            <div class="duration">
              <i class="fas fa-calendar-alt" aria-hidden="true"></i>
              <time datetime="<?php echo $project['startDate']; ?>">
                <?php
                  echo date('M Y', strtotime($project['startDate'])) . ' - ' .
                    ($project['endDate'] ? date('M Y', strtotime($project['endDate'])) : 'Present');
                  ?>
              </time>
            </div>
          </div>
          <div class="header-links">
            <?php if (!empty($project['links']['github'])): ?>
            <a href="<?php echo $project['links']['github']; ?>" target="_blank" class="project-link"
              data-tooltip="View on GitHub" itemprop="codeRepository">
              <i class="fab fa-github" aria-hidden="true"></i>
              <span class="sr-only">View <?php echo $project['name']; ?> on GitHub</span>
            </a>
            <?php endif; ?>
            <?php if (!empty($project['links']['website'])): ?>
            <a href="<?php echo $project['links']['website']; ?>" target="_blank" class="project-link"
              data-tooltip="Visit Website" itemprop="url">
              <i class="fas fa-globe" aria-hidden="true"></i>
              <span class="sr-only">Visit <?php echo $project['name']; ?> website</span>
            </a>
            <?php endif; ?>
            <?php if (empty($project['links']) || (empty($project['links']['github']) && empty($project['links']['website']))): ?>
            <span class="project-link private-link" data-tooltip="Private Project">
              <i class="fas fa-lock" aria-hidden="true"></i>
              <span class="sr-only">Private project</span>
            </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="project-content">
          <p class="description" itemprop="description">
            <?php
              $truncated_description = strlen($project['description']) > 150 ?
                substr($project['description'], 0, 150) . '...' :
                $project['description'];
              echo $truncated_description;
              ?>
          </p>

          <div class="skills-list" itemprop="applicationCategory">
            <?php
              $displaySkills = array_slice($project['skills'], 0, 3);
              foreach ($displaySkills as $skill):
              ?>
            <span class="skill-tag" data-tooltip="<?php echo $skill; ?>"><?php echo $skill; ?></span>
            <?php endforeach; ?>
            <?php if (count($project['skills']) > 3): ?>
            <span class="skill-tag more-skills" data-tooltip="Click to view all technologies">
              +<?php echo count($project['skills']) - 3; ?> more
            </span>
            <?php endif; ?>
          </div>

          <button class="view-details-btn" data-project-index="<?php echo $projectIndex; ?>"
            aria-label="View details of <?php echo $project['name']; ?>">
            View Details <i class="fas fa-external-link-alt" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Project Modal -->
  <div class="project-modal" id="projectModal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <button class="modal-close"><i class="fas fa-times"></i></button>
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title"></h2>
          <div class="modal-meta">
            <div class="modal-duration">
              <i class="fas fa-calendar-alt"></i>
              <span></span>
            </div>
            <div class="modal-links">
              <!-- Project links will be populated by JavaScript -->
            </div>
          </div>
        </div>

        <div class="modal-description">
          <!-- Description will be populated by JavaScript -->
        </div>

        <div class="modal-collaborators">
          <h3><i class="fas fa-users"></i> Collaborators</h3>
          <div class="collaborators-list">
            <!-- Collaborators will be populated by JavaScript -->
          </div>
        </div>

        <div class="modal-skills">
          <h3><i class="fas fa-code"></i> Technologies Used</h3>
          <div class="skills-list">
            <!-- Skills will be populated by JavaScript -->
          </div>
        </div>

        <div class="modal-media">
          <div class="media-tabs">
            <button class="tab-btn active" data-tab="images">
              <i class="fas fa-images"></i> Images
            </button>
            <button class="tab-btn" data-tab="videos">
              <i class="fas fa-video"></i> Videos
            </button>
          </div>
          <div class="media-content">
            <div class="tab-content images active">
              <div class="media-grid">
                <!-- Images will be populated by JavaScript -->
              </div>
            </div>
            <div class="tab-content videos">
              <div class="media-grid">
                <!-- Videos will be populated by JavaScript -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Media Preview Modal -->
  <div class="media-preview-modal" id="mediaPreviewModal">
    <div class="preview-overlay"></div>
    <div class="preview-container">
      <button class="preview-close"><i class="fas fa-times"></i></button>
      <div class="preview-content">
        <!-- Media preview content will be populated by JavaScript -->
      </div>
      <div class="preview-actions">
        <a href="#" class="preview-download" download>
          <i class="fas fa-download"></i> Download
        </a>
        <a href="#" class="preview-open-link" target="_blank">
          <i class="fas fa-external-link-alt"></i> Open in New Tab
        </a>
      </div>
    </div>
  </div>

  <!-- Skills Section -->
  <div class="skills-section" data-aos="fade-up">
    <h2>Tech Stack & Tools</h2>
    <div class="skills-grid">
      <?php foreach ($profileData['techStack'] as $tech): ?>
      <a href="<?php echo $tech['link']; ?>" target="_blank" class="skill-item" data-aos="zoom-in"
        data-tooltip="<?php echo $tech['name']; ?>">
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
          <a href="<?php echo $award['link']; ?>" target="_blank" class="award-link" data-tooltip="View award details">
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

    <!-- Language Distribution -->
    <?php
    // Sort languages by commit count
    $langData = $githubStatsData['langCommitCount'];
    arsort($langData);
    $languages = array_keys($langData);
    $commitCounts = array_values($langData);
    ?>
    <div class="language-distribution" data-aos="fade-up"
      data-languages="<?php echo htmlspecialchars(json_encode($languages)); ?>"
      data-commits="<?php echo htmlspecialchars(json_encode($commitCounts)); ?>">
      <h3>Language Distribution</h3>
      <div class="chart-container">
        <canvas id="languageChart"></canvas>
      </div>
    </div>

    <!-- Top Programming Languages -->
    <?php
    // Get top 6 languages with their stats
    $topLanguages = array_slice($langData, 0, 6, true);
    $totalCommits = array_sum($langData);
    ?>
    <div class="top-languages" data-aos="fade-up">
      <h3>Top Programming Languages</h3>
      <div class="languages-grid">
        <?php foreach ($topLanguages as $lang => $commits):
          $repoCount = $githubStatsData['langRepoCount'][$lang] ?? 0;
          $starCount = $githubStatsData['langStarCount'][$lang] ?? 0;
          $percentage = ($commits / $totalCommits) * 100;
          $lang_icon = strtolower($lang);
          $replacements = [
            'c++' => 'cpp',
            'unknown' => 'github',
            'c#' => 'cs',
            'objective-c' => 'objectivec',
            'jupyter notebook' => 'jupyter',
            'shell' => 'bash'
          ];
        ?>
        <div class="language-card" data-aos="zoom-in">
          <div class="language-header">
            <div class="language-icon">
              <img src="https://skillicons.dev/icons?i=<?php
                                                          echo isset($replacements[$lang_icon]) ? $replacements[$lang_icon] : $lang_icon;
                                                          ?>" alt="<?php echo $lang; ?>">
            </div>
            <div class="language-name">
              <h4><?php echo $lang; ?></h4>
              <p><?php echo number_format($percentage, 1); ?>% of commits</p>
            </div>
          </div>
          <div class="language-stats">
            <div class="stat-item" data-tooltip="<?php echo $repoCount; ?> Repositories">
              <i class="fas fa-code-branch"></i>
              <span><?php echo $repoCount; ?></span>
            </div>
            <div class="stat-item" data-tooltip="<?php echo $commits; ?> Commits">
              <i class="fas fa-code"></i>
              <span><?php echo $commits; ?></span>
            </div>
            <div class="stat-item" data-tooltip="<?php echo $starCount; ?> Stars">
              <i class="fas fa-star"></i>
              <span><?php echo $starCount; ?></span>
            </div>
          </div>
          <div class="language-progress">
            <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Top Repositories -->
    <?php
    // Get top 5 repositories by commit count with their descriptions
    $topRepos = array_slice($githubStatsData['repoCommitCount'], 0, 5, true);
    $githubBaseUrl = rtrim($githubStatsData['user']['htmlUrl'], '/');
    ?>
    <div class="top-repos" data-aos="fade-up">
      <h3>Top Repositories by Commits</h3>
      <div class="repos-list">
        <?php foreach ($topRepos as $repo => $commits):
          $stars = $githubStatsData['repoStarCount'][$repo] ?? 0;
          $description = $githubStatsData['repoCommitCountDescriptions'][$repo] ?? 'No description available';
        ?>
        <div class="repo-card" data-aos="fade-up">
          <div class="repo-header">
            <a href="<?php echo $githubBaseUrl . '/' . $repo; ?>" target="_blank" class="repo-name"
              data-tooltip="View repository">
              <?php echo $repo; ?>
            </a>
            <div class="repo-stats">
              <div class="stat-item" data-tooltip="<?php echo $commits; ?> Commits">
                <i class="fas fa-code"></i>
                <span><?php echo $commits; ?></span>
              </div>
              <div class="stat-item" data-tooltip="<?php echo $stars; ?> Stars">
                <i class="fas fa-star"></i>
                <span><?php echo $stars; ?></span>
              </div>
            </div>
          </div>
          <p class="repo-description"><?php echo $description; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Contact Section -->
    <div class="contact-section" data-aos="fade-up">
      <div class="contact-bg"></div>
      <div class="contact-content">
        <h2>Let's Connect</h2>
        <p>Feel free to reach out for collaborations or just a friendly chat</p>
        <div class="social-links">
          <?php
          $delay = 100;
          foreach ($profileData['socialLinks'] as $social):
            $icon = match ($social['title']) {
              'GitHub' => 'fab fa-github',
              'LinkedIn' => 'fab fa-linkedin-in',
              'Twitter' => 'fab fa-twitter',
              'Facebook' => 'fab fa-facebook-f',
              'Instagram' => 'fab fa-instagram',
              'Telegram' => 'fab fa-telegram-plane',
              'YouTube' => 'fab fa-youtube',
              default => 'fas fa-link'
            };
          ?>
          <a href="<?php echo $social['link']; ?>" target="_blank" class="social-link"
            data-tooltip="<?php echo $social['title']; ?>" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
            <i class="<?php echo $icon; ?>"></i>
          </a>
          <?php
            $delay += 50;
          endforeach;
          ?>
        </div>
      </div>
    </div>
  </div>
</section>