document.addEventListener("DOMContentLoaded", function () {
  // Find commit activity container
  const commitActivity = document.querySelector(".commit-activity");
  if (!commitActivity) return;

  try {
    // Get data from attributes
    const quarters = JSON.parse(commitActivity.dataset.quarters || "[]");
    const commits = JSON.parse(commitActivity.dataset.commits || "[]");

    // Remove data attributes after parsing
    commitActivity.removeAttribute("data-quarters");
    commitActivity.removeAttribute("data-commits");

    // Create commit activity chart
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
});
