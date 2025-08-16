<?php
// Global configuration for the Stationery Store

// Environment-based configuration with sensible defaults for local dev
const DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
const DB_NAME = getenv('DB_NAME') ?: 'stationery_store';
const DB_USER = getenv('DB_USER') ?: 'root';
const DB_PASS = getenv('DB_PASS') ?: '';
const DB_CHARSET = 'utf8mb4';

// Base URL (no trailing slash). Example: 'http://localhost:8000'
const BASE_URL = getenv('BASE_URL') ?: '';

// App settings
date_default_timezone_set('UTC');