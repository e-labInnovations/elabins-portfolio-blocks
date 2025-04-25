#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get version from plugin file
VERSION=$(grep -oP "Version:\s*\K[0-9.]+" elabins-portfolio-blocks.php)
if [ -z "$VERSION" ]; then
    echo -e "${RED}❌ Could not extract version from plugin file${NC}"
    exit 1
fi
PLUGIN_NAME="elabins-portfolio-blocks"

echo -e "${BLUE}=== Building ${PLUGIN_NAME} v${VERSION} ===${NC}\n"

# Build assets
echo -e "${YELLOW}🛠️  Building assets...${NC}"
npm run build
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Build failed!${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Assets built successfully${NC}\n"

# Create temporary directory
TEMP_DIR="temp-${PLUGIN_NAME}"
mkdir -p "${TEMP_DIR}"
echo -e "${YELLOW}📁 Created temporary directory: ${TEMP_DIR}${NC}"

# Copy files to temporary directory, excluding those in .buildignore
echo -e "${YELLOW}📦 Copying files...${NC}"
rsync -av --exclude-from=.buildignore . "${TEMP_DIR}/"
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ File copy failed!${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Files copied successfully${NC}\n"

# Create zip file
echo -e "${YELLOW}🗜️  Creating zip file...${NC}"
ZIP_NAME="${PLUGIN_NAME}-v${VERSION}.zip"
cd "${TEMP_DIR}"
zip -r "../${ZIP_NAME}" . > /dev/null
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Zip creation failed!${NC}"
    exit 1
fi
cd ..
echo -e "${GREEN}✅ Zip file created: ${ZIP_NAME}${NC}\n"

# Clean up
echo -e "${YELLOW}🧹 Cleaning up...${NC}"
rm -rf "${TEMP_DIR}"
echo -e "${GREEN}✅ Cleanup complete${NC}\n"

echo -e "${BLUE}=== Build completed successfully! ===${NC}"
echo -e "Plugin zip: ${GREEN}${ZIP_NAME}${NC}" 