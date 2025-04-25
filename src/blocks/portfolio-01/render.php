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
      <p>
        <?php echo $profileData['profile']['about']; ?>
      </p>
    </div>
    <div class="about-card" data-aos="fade-left" data-aos-delay="300">
      <h2>Quick Facts</h2>
      <ul>
        <?php foreach ($profileData['profile']['facts'] as $fact) : ?>
          <li><i class="<?php echo $fact['icon']; ?>"></i> <?php echo $fact['content']; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Skills Section -->
  <div class="skills-section" data-aos="fade-up">
    <h2>Tech Stack & Tools</h2>
    <div class="skills-grid">
      <!-- Programming Languages -->
      <?php foreach ($profileData['techStack'] as $tech) : ?>
        <a href="<?php echo $tech['link']; ?>" target="_blank">
          <div class="skill-item" data-aos="zoom-in" data-aos-delay="100">
            <div class="skill-icon">
              <img src="<?php echo $tech['image']; ?>" alt="<?php echo $tech['name']; ?>" />
            </div>
            <span class="skill-name"><?php echo $tech['name']; ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>