document.addEventListener("DOMContentLoaded", function () {
  // Initialize AOS
  AOS.init({
    duration: 800,
    easing: "ease-in-out",
    once: false,
    mirror: true,
    offset: 50,
  });

  // Wait for Tippy to be available
  if (typeof tippy !== "undefined") {
    // Initialize Tippy.js tooltips
    tippy("[data-tooltip]", {
      content: (reference) => reference.getAttribute("data-tooltip"),
      animation: "shift-away",
      arrow: true,
      placement: "top",
      theme: "gradient",
      duration: [200, 150],
      allowHTML: true,
      interactive: true,
      appendTo: document.body,
    });
  } else {
    console.warn("Tippy.js not loaded yet");
  }

  // Create Commit Activity Chart
  const commitActivity = document.querySelector(".commit-activity");
  if (commitActivity) {
    try {
      const quarters = JSON.parse(commitActivity.dataset.quarters || "[]");
      const commits = JSON.parse(commitActivity.dataset.commits || "[]");

      // Remove data attributes after parsing
      commitActivity.removeAttribute("data-quarters");
      commitActivity.removeAttribute("data-commits");

      const commitChart = document.getElementById("commitChart");
      if (commitChart) {
        new Chart(commitChart, {
          type: "line",
          data: {
            labels: quarters,
            datasets: [
              {
                label: "Commits",
                data: commits,
                backgroundColor: "rgba(67, 206, 162, 0.1)",
                borderColor: "rgba(67, 206, 162, 1)",
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: "rgba(67, 206, 162, 1)",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            chartArea: {
              backgroundColor: "rgba(255, 255, 255, 0.1)",
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  color: "rgba(0, 0, 0, 0.05)",
                  drawBorder: false,
                },
                ticks: {
                  color: "#666",
                  stepSize: 1,
                },
              },
              x: {
                grid: {
                  display: false,
                },
                ticks: {
                  color: "#666",
                },
              },
            },
            plugins: {
              legend: {
                display: false,
              },
              tooltip: {
                backgroundColor: "rgba(255, 255, 255, 0.95)",
                titleColor: "#185a9d",
                bodyColor: "#666",
                borderColor: "rgba(0, 0, 0, 0.1)",
                borderWidth: 1,
                padding: 10,
                displayColors: false,
                callbacks: {
                  title: (items) => `Quarter: ${items[0].label}`,
                  label: (item) => `Commits: ${item.raw}`,
                },
              },
            },
            animation: {
              duration: 1000,
              easing: "easeInOutQuart",
            },
          },
        });
      }
    } catch (error) {
      console.error("Error creating commit activity chart:", error);
      commitActivity.style.display = "none";
    }
  }

  // Create Language Distribution Chart
  const languageDistribution = document.querySelector(".language-distribution");
  if (languageDistribution) {
    try {
      const languages = JSON.parse(
        languageDistribution.dataset.languages || "[]",
      );
      const commits = JSON.parse(languageDistribution.dataset.commits || "[]");

      // Remove data attributes after parsing
      languageDistribution.removeAttribute("data-languages");
      languageDistribution.removeAttribute("data-commits");

      const languageChart = document.getElementById("languageChart");
      if (languageChart) {
        new Chart(languageChart, {
          type: "doughnut",
          data: {
            labels: languages,
            datasets: [
              {
                data: commits,
                backgroundColor: [
                  "rgba(67, 206, 162, 0.8)",
                  "rgba(24, 90, 157, 0.8)",
                  "rgba(255, 99, 132, 0.8)",
                  "rgba(54, 162, 235, 0.8)",
                  "rgba(255, 206, 86, 0.8)",
                  "rgba(75, 192, 192, 0.8)",
                  "rgba(153, 102, 255, 0.8)",
                  "rgba(255, 159, 64, 0.8)",
                ],
                borderColor: "rgba(255, 255, 255, 0.1)",
                borderWidth: 2,
                hoverOffset: 10,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "60%",
            plugins: {
              legend: {
                position: "right",
                labels: {
                  color: "#666",
                  padding: 20,
                  font: {
                    size: 12,
                  },
                  usePointStyle: true,
                  pointStyle: "circle",
                },
              },
              tooltip: {
                backgroundColor: "rgba(255, 255, 255, 0.95)",
                titleColor: "#185a9d",
                bodyColor: "#666",
                borderColor: "rgba(0, 0, 0, 0.1)",
                borderWidth: 1,
                padding: 10,
                callbacks: {
                  label: (context) => {
                    const label = context.label || "";
                    const value = context.raw || 0;
                    const total = context.dataset.data.reduce(
                      (a, b) => a + b,
                      0,
                    );
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: ${value} commits (${percentage}%)`;
                  },
                },
              },
            },
            animation: {
              duration: 1000,
              easing: "easeInOutQuart",
            },
          },
        });
      }
    } catch (error) {
      console.error("Error creating language distribution chart:", error);
      languageDistribution.style.display = "none";
    }
  }

  // Project Modal Functionality
  const projectsSection = document.querySelector(".projects-section");
  const projectsData = projectsSection
    ? JSON.parse(projectsSection.dataset.projects || "[]")
    : [];
  // Remove the data attribute after parsing
  if (projectsSection) {
    projectsSection.removeAttribute("data-projects");
  }

  const modal = document.getElementById("projectModal");
  const mediaPreviewModal = document.getElementById("mediaPreviewModal");
  const modalOverlay = modal?.querySelector(".modal-overlay");
  const modalClose = modal?.querySelector(".modal-close");
  const viewDetailsBtns = document.querySelectorAll(".view-details-btn");
  let currentProject = null;
  let currentMediaUrl = null;

  function openModal(project) {
    if (!modal) return;

    currentProject = project;
    document.body.style.overflow = "hidden";
    modal.classList.add("active");

    // Populate modal content
    const modalTitle = modal.querySelector(".modal-title");
    const modalDuration = modal.querySelector(".modal-duration span");
    const modalDescription = modal.querySelector(".modal-description");
    const modalLinks = modal.querySelector(".modal-links");
    const modalCollaborators = modal.querySelector(".modal-collaborators");
    const modalSkills = modal.querySelector(".modal-skills");
    const imagesTab = modal.querySelector(".tab-content.images .media-grid");
    const videosTab = modal.querySelector(".tab-content.videos .media-grid");

    // Set basic information
    modalTitle.textContent = project.name;
    modalDuration.textContent = `${formatDate(project.startDate)} - ${project.endDate ? formatDate(project.endDate) : "Present"}`;
    modalDescription.innerHTML = `<p>${project.description}</p>`;

    // Handle links
    console.log(
      project.links && Object.keys(project.links).length > 0,
      project.links,
    );
    if (project.links && (project.links.github || project.links.website)) {
      let linksHTML = "";
      if (project.links.github) {
        linksHTML += `
          <a href="${project.links.github}" target="_blank" class="project-link">
            <i class="fab fa-github"></i> Repo
          </a>
        `;
      }
      if (project.links.website) {
        linksHTML += `
          <a href="${project.links.website}" target="_blank" class="project-link">
            <i class="fas fa-globe"></i> Website
          </a>
        `;
      }
      modalLinks.innerHTML = linksHTML;
    } else {
      modalLinks.innerHTML = `
        <span class="project-link private-link" data-tooltip="Private Project">
          <i class="fas fa-lock"></i> Private
        </span>
      `;
    }

    // Handle media tabs visibility
    const mediaTabs = modal.querySelector(".media-tabs");
    const mediaSection = modal.querySelector(".modal-media");
    const hasImages = project.media?.images?.length > 0;
    const hasVideos = project.media?.videos?.length > 0;

    if (!hasImages && !hasVideos) {
      mediaSection.style.display = "none";
    } else {
      mediaSection.style.display = "block";
      mediaTabs.style.display = hasImages && hasVideos ? "flex" : "none";

      // Show appropriate tab
      if (!hasImages) {
        switchTab("videos");
      } else if (!hasVideos) {
        switchTab("images");
      }
    }

    // Handle images
    if (hasImages) {
      imagesTab.innerHTML = project.media.images
        .map(
          (image) => `
        <div class="media-item">
          <img src="${image}" alt="Project Images" loading="lazy" />
          <div class="media-overlay">
            <button class="preview-btn" data-media="${image}" data-type="image">
              <i class="fas fa-expand"></i>
            </button>
            <a href="${image}" target="_blank" class="open-btn" data-tooltip="Open in new tab">
              <i class="fas fa-external-link-alt"></i>
            </a>
          </div>
        </div>
      `,
        )
        .join("");
    } else {
      imagesTab.innerHTML = '<p class="no-media">No images available</p>';
    }

    // Handle videos
    if (hasVideos) {
      videosTab.innerHTML = project.media.videos
        .map((videoUrl) => {
          const youtubeId = getYouTubeVideoId(videoUrl);
          if (youtubeId) {
            return `
              <div class="media-item video">
                <div class="video-thumbnail" style="background-image: url(https://img.youtube.com/vi/${youtubeId}/hqdefault.jpg)"></div>
                <div class="media-overlay">
                    <button class="preview-btn" data-media="https://www.youtube.com/embed/${youtubeId}" data-type="video" data-youtube-id="${youtubeId}">
                      <i class="fas fa-play"></i>
                    </button>
                    <a href="https://www.youtube.com/watch?v=${youtubeId}" target="_blank" class="open-btn" data-tooltip="Open in YouTube">
                      <i class="fab fa-youtube"></i>
                    </a>
                  </div>
              </div>
            `;
          } else {
            return `
              <div class="media-item video">
                <video src="${videoUrl}" preload="metadata" poster>
                  Your browser does not support the video tag.
                </video>
                <div class="media-overlay">
                  <button class="preview-btn" data-media="${videoUrl}" data-type="video">
                    <i class="fas fa-play"></i>
                  </button>
                  <a href="${videoUrl}" target="_blank" class="open-btn" data-tooltip="Open video in new tab">
                    <i class="fas fa-external-link-alt"></i>
                  </a>
                </div>
              </div>
            `;
          }
        })
        .join("");
    } else {
      videosTab.innerHTML = '<p class="no-media">No videos available</p>';
    }

    // Handle collaborators
    if (project.collaborators?.length) {
      modalCollaborators.style.display = "block";
      const collaboratorsList = modalCollaborators.querySelector(
        ".collaborators-list",
      );
      collaboratorsList.innerHTML = project.collaborators
        .map((collaborator) => {
          if (typeof collaborator === "string") {
            return `<span class="collaborator">${collaborator}</span>`;
          } else {
            return collaborator.link
              ? `<a href="${collaborator.link}" target="_blank" class="collaborator">
              <i class="fas fa-user"></i> 
              ${collaborator.name}
              <i class="fas fa-external-link-alt"></i>
            </a>`
              : `<span class="collaborator"><i class="fas fa-user"></i> ${collaborator.name}</span>`;
          }
        })
        .join("");
    } else {
      modalCollaborators.style.display = "none";
    }

    // Handle skills
    if (project.skills?.length) {
      modalSkills.style.display = "block";
      const skillsList = modalSkills.querySelector(".skills-list");
      skillsList.innerHTML = project.skills
        .map(
          (skill) =>
            `<span class="skill-tag" data-tooltip="${skill}">${skill}</span>`,
        )
        .join("");
    } else {
      modalSkills.style.display = "none";
    }

    // Initialize tooltips for new elements
    if (typeof tippy !== "undefined") {
      tippy("[data-tooltip]", {
        content: (reference) => reference.getAttribute("data-tooltip"),
        animation: "shift-away",
        arrow: true,
        placement: "top",
      });
    }
  }

  function closeModal() {
    if (!modal) return;
    document.body.style.overflow = "";
    modal.classList.remove("active");
    currentProject = null;
  }

  function openMediaPreview(mediaUrl, type, youtubeId = null) {
    if (!mediaPreviewModal) return;

    currentMediaUrl = mediaUrl;
    const previewContent = mediaPreviewModal.querySelector(".preview-content");
    const openLink = mediaPreviewModal.querySelector(".preview-open-link");
    const downloadLink = mediaPreviewModal.querySelector(".preview-download");

    if (type === "image") {
      previewContent.innerHTML = `<img src="${mediaUrl}" alt="Preview" />`;
      openLink.href = mediaUrl;
      downloadLink.href = mediaUrl;
      downloadLink.style.display = "block";
    } else if (type === "video") {
      if (youtubeId) {
        previewContent.innerHTML = `
          <iframe 
            src="https://www.youtube.com/embed/${youtubeId}?autoplay=1" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
          </iframe>
        `;
        openLink.href = `https://www.youtube.com/watch?v=${youtubeId}`;
        downloadLink.style.display = "none";
      } else {
        previewContent.innerHTML = `
          <video src="${mediaUrl}" controls autoplay>
            Your browser does not support the video tag.
          </video>
        `;
        openLink.href = mediaUrl;
        downloadLink.href = mediaUrl;
        downloadLink.style.display = "block";
      }
    }

    document.body.style.overflow = "hidden";
    mediaPreviewModal.classList.add("active");
  }

  function closeMediaPreview() {
    if (!mediaPreviewModal) return;
    document.body.style.overflow = modal?.classList.contains("active")
      ? "hidden"
      : "";
    mediaPreviewModal.classList.remove("active");
    mediaPreviewModal.querySelector(".preview-content").innerHTML = "";
    currentMediaUrl = null;
  }

  function switchTab(tabName) {
    if (!modal) return;

    const tabs = modal.querySelectorAll(".tab-btn");
    const contents = modal.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
      tab.classList.toggle("active", tab.dataset.tab === tabName);
    });

    contents.forEach((content) => {
      content.classList.toggle("active", content.classList.contains(tabName));
    });
  }

  function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
      month: "short",
      year: "numeric",
    });
  }

  function getYouTubeVideoId(url) {
    // Support both youtu.be and youtube.com URLs
    const regExp =
      /(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|&v=))([^#&?]*).*/;
    const match = url?.match(regExp);
    return match && match[1].length === 11 ? match[1] : null;
  }

  // Event listeners for project modal
  viewDetailsBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const projectIndex = parseInt(btn.dataset.projectIndex);
      if (projectsData[projectIndex]) {
        openModal(projectsData[projectIndex]);
      }
    });
  });

  if (modal) {
    // Tab switching
    modal.querySelectorAll(".tab-btn").forEach((btn) => {
      btn.addEventListener("click", () => switchTab(btn.dataset.tab));
    });

    // Media preview
    modal.addEventListener("click", (e) => {
      const previewBtn = e.target.closest(".preview-btn");
      if (previewBtn) {
        const { media, type, youtubeId } = previewBtn.dataset;
        openMediaPreview(media, type, youtubeId);
      }
    });

    // Close modal
    modalOverlay?.addEventListener("click", closeModal);
    modalClose?.addEventListener("click", closeModal);
  }

  if (mediaPreviewModal) {
    const previewOverlay = mediaPreviewModal.querySelector(".preview-overlay");
    const previewClose = mediaPreviewModal.querySelector(".preview-close");

    previewOverlay?.addEventListener("click", closeMediaPreview);
    previewClose?.addEventListener("click", closeMediaPreview);
  }

  // Close modals on escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      if (mediaPreviewModal?.classList.contains("active")) {
        closeMediaPreview();
      } else if (modal?.classList.contains("active")) {
        closeModal();
      }
    }
  });
});
