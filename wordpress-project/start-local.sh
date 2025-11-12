#!/bin/bash

###############################################################################
#                  CNP News - Local Development Launcher                     #
#                        One-command WordPress Setup                         #
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="$PROJECT_DIR/docker-compose.yml"

echo -e "${BLUE}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     CNP News - Local WordPress Development Setup       ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════╝${NC}\n"

# Check Docker installation
echo -e "${YELLOW}🔍 Checking Docker installation...${NC}"
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker is not installed!${NC}"
    echo "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose is not installed!${NC}"
    echo "Please install Docker Compose with: brew install docker-compose"
    exit 1
fi

echo -e "${GREEN}✓ Docker is installed${NC}"
echo -e "${GREEN}✓ Docker Compose is installed${NC}\n"

# Check if docker-compose.yml exists
if [ ! -f "$COMPOSE_FILE" ]; then
    echo -e "${RED}❌ docker-compose.yml not found!${NC}"
    echo "Make sure you're in the wordpress-project directory"
    exit 1
fi

# Stop existing containers if running
echo -e "${YELLOW}🛑 Checking for existing containers...${NC}"
if docker-compose -f "$COMPOSE_FILE" ps | grep -q "Up"; then
    echo -e "${YELLOW}Found running containers. Stopping...${NC}"
    docker-compose -f "$COMPOSE_FILE" down
    sleep 2
fi

# Start services
echo -e "\n${BLUE}🚀 Starting Docker services...${NC}"
docker-compose -f "$COMPOSE_FILE" up -d

# Wait for MySQL
echo -e "\n${YELLOW}⏳ Waiting for MySQL to be ready (this may take 30-60 seconds)...${NC}"
sleep 10

# Check MySQL health
for i in {1..30}; do
    if docker exec cnp-mysql mysqladmin ping -h localhost &> /dev/null; then
        echo -e "${GREEN}✓ MySQL is ready${NC}"
        break
    fi
    echo -n "."
    sleep 2
    if [ $i -eq 30 ]; then
        echo -e "${RED}❌ MySQL failed to start${NC}"
        echo "Check logs with: docker-compose logs mysql"
        exit 1
    fi
done

# Check WordPress container
echo -e "\n${YELLOW}⏳ Waiting for WordPress to be ready...${NC}"
sleep 20

# Check if WordPress is responding
for i in {1..10}; do
    if curl -s http://localhost | grep -q "wordpress\|wp-"; then
        echo -e "${GREEN}✓ WordPress is responding${NC}"
        break
    fi
    echo -n "."
    sleep 3
    if [ $i -eq 10 ]; then
        echo -e "${YELLOW}⚠ WordPress is loading (may take another moment)${NC}"
    fi
done

# Display access information
echo -e "\n${GREEN}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║              🎉 Setup Complete! 🎉                    ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════╝${NC}\n"

echo -e "${BLUE}📱 Access Points:${NC}"
echo -e "   ${YELLOW}WordPress:${NC}      ${GREEN}http://localhost${NC}"
echo -e "   ${YELLOW}Admin Panel:${NC}     ${GREEN}http://localhost/wp-admin${NC}"
echo -e "   ${YELLOW}phpMyAdmin:${NC}     ${GREEN}http://localhost:8080${NC}"
echo -e "   ${YELLOW}Adminer:${NC}        ${GREEN}http://localhost:8081${NC}\n"

echo -e "${BLUE}📊 Database Access:${NC}"
echo -e "   ${YELLOW}Server:${NC}   mysql (docker-internal)"
echo -e "   ${YELLOW}Database:${NC} cnpnews_wp"
echo -e "   ${YELLOW}User:${NC}     cnpnews"
echo -e "   ${YELLOW}Password:${NC} cnpnews_password\n"

echo -e "${BLUE}🎯 Next Steps:${NC}"
echo -e "   1. Open ${GREEN}http://localhost${NC} in your browser"
echo -e "   2. Complete the WordPress installation wizard"
echo -e "   3. Log in with your credentials"
echo -e "   4. Go to Appearance → Themes"
echo -e "   5. Activate 'CNP News Theme'"
echo -e "   6. Start editing!\n"

echo -e "${BLUE}📝 Useful Commands:${NC}"
echo -e "   ${YELLOW}View logs:${NC}           docker-compose logs -f"
echo -e "   ${YELLOW}Stop services:${NC}        docker-compose down"
echo -e "   ${YELLOW}Shell access:${NC}         docker exec -it cnp-wordpress bash"
echo -e "   ${YELLOW}Database access:${NC}      docker exec -it cnp-mysql mysql -u cnpnews -p"
echo -e "   ${YELLOW}Remove everything:${NC}    docker-compose down -v\n"

echo -e "${BLUE}📖 Documentation:${NC}"
echo -e "   See ${GREEN}LOCAL_SETUP.md${NC} for detailed instructions\n"

# Display status
echo -e "${BLUE}🔄 Current Service Status:${NC}"
docker-compose -f "$COMPOSE_FILE" ps

echo -e "\n${GREEN}✅ All systems go!${NC} 🚀\n"
