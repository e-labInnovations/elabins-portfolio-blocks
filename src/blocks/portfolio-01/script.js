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
});
