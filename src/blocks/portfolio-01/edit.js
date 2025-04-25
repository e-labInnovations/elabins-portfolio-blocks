import "./editor.scss";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  Button,
  Notice,
  Placeholder,
  Spinner,
} from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import apiFetch from "@wordpress/api-fetch";

export default function Edit({ attributes, setAttributes }) {
  const blockProps = useBlockProps();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [previewData, setPreviewData] = useState(null);

  const { portfolioJsonUrl = "", githubStatsJsonUrl = "" } = attributes;

  const validateAndFetchJson = async () => {
    setLoading(true);
    setError(null);

    try {
      // Fetch portfolio data
      const portfolioResponse = await fetch(portfolioJsonUrl);
      if (!portfolioResponse.ok) {
        throw new Error("Failed to fetch portfolio data");
      }
      const portfolioData = await portfolioResponse.json();

      // Fetch GitHub stats
      const githubResponse = await fetch(githubStatsJsonUrl);
      if (!githubResponse.ok) {
        throw new Error("Failed to fetch GitHub stats");
      }
      const githubData = await githubResponse.json();

      // Validate required fields in portfolio data
      if (!portfolioData.profile || !portfolioData.projects) {
        throw new Error("Invalid portfolio data structure");
      }

      // Validate required fields in GitHub stats
      if (!githubData.user || !githubData.langCommitCount) {
        throw new Error("Invalid GitHub stats structure");
      }

      setPreviewData({ portfolio: portfolioData, github: githubData });
      setError(null);
    } catch (err) {
      setError(err.message);
      setPreviewData(null);
    } finally {
      setLoading(false);
    }
  };

  // Effect to validate URLs when they change
  useEffect(() => {
    if (portfolioJsonUrl && githubStatsJsonUrl) {
      validateAndFetchJson();
    }
  }, [portfolioJsonUrl, githubStatsJsonUrl]);

  return (
    <>
      <InspectorControls>
        <PanelBody title={__("Portfolio Settings", "elabins-portfolio-blocks")}>
          <TextControl
            label={__("Portfolio JSON URL", "elabins-portfolio-blocks")}
            value={portfolioJsonUrl}
            onChange={(value) => setAttributes({ portfolioJsonUrl: value })}
            help={__(
              "Enter the URL of your portfolio data JSON file",
              "elabins-portfolio-blocks",
            )}
          />
          <TextControl
            label={__("GitHub Stats JSON URL", "elabins-portfolio-blocks")}
            value={githubStatsJsonUrl}
            onChange={(value) => setAttributes({ githubStatsJsonUrl: value })}
            help={__(
              "Enter the URL of your GitHub stats JSON file",
              "elabins-portfolio-blocks",
            )}
          />
          <Button
            isPrimary
            onClick={validateAndFetchJson}
            disabled={!portfolioJsonUrl || !githubStatsJsonUrl || loading}
          >
            {loading
              ? __("Validating...", "elabins-portfolio-blocks")
              : __("Validate JSON", "elabins-portfolio-blocks")}
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {loading ? (
          <Placeholder>
            <Spinner />
            <p>{__("Validating JSON data...", "elabins-portfolio-blocks")}</p>
          </Placeholder>
        ) : error ? (
          <Notice status="error" isDismissible={false}>
            {error}
          </Notice>
        ) : !previewData ? (
          <Placeholder
            icon="portfolio"
            label={__("Portfolio Block", "elabins-portfolio-blocks")}
            instructions={__(
              "Enter your portfolio and GitHub stats JSON URLs in the block settings.",
              "elabins-portfolio-blocks",
            )}
          >
            <Button
              isPrimary
              onClick={() => {
                const sidebarPanel = document.querySelector(
                  ".interface-complementary-area",
                );
                if (sidebarPanel) {
                  sidebarPanel.style.display = "block";
                }
              }}
            >
              {__("Open Settings", "elabins-portfolio-blocks")}
            </Button>
          </Placeholder>
        ) : (
          <div className="portfolio-preview">
            <div className="preview-header">
              <h2>{previewData.portfolio.profile.name}</h2>
              <p>{previewData.portfolio.profile.title}</p>
            </div>
            <div className="preview-stats">
              <div className="stat-item">
                <span className="stat-label">
                  {__("Projects", "elabins-portfolio-blocks")}
                </span>
                <span className="stat-value">
                  {previewData.portfolio.projects.length}
                </span>
              </div>
              <div className="stat-item">
                <span className="stat-label">
                  {__("GitHub Repos", "elabins-portfolio-blocks")}
                </span>
                <span className="stat-value">
                  {previewData.github.user.publicRepos}
                </span>
              </div>
              <div className="stat-item">
                <span className="stat-label">
                  {__("Total Stars", "elabins-portfolio-blocks")}
                </span>
                <span className="stat-value">
                  {Object.values(previewData.github.repoStarCount).reduce(
                    (a, b) => a + b,
                    0,
                  )}
                </span>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
