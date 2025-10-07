# Calvary Songs Book API Brief

## The foundation of your project

Calvary Songs Book API is a Laravel 11 backend that powers a digital hymnal for the Calvary community.
It manages canonical song metadata, lyrics, sheet music references, and worship resources while enforcing role-based access.
The service separates administrative curation from public consumption, ensuring data consistency across mobile clients.

## High-level overview of what you're building

The platform exposes RESTful endpoints for both admin and user applications, supporting song browsing, search, categorization, and localization.
Admins can authenticate, create and maintain songs, categories, styles, and language mappings, while end users consume cached, read-optimized endpoints.
Supporting features include version checks for mobile releases, CSV import tooling, and cache-backed filter discovery to keep clients responsive.
The stack relies on Laravel's service container, caching, queue-ready architecture, and Sanctum-based token authentication to keep the API secure and extensible.

## Core requirements and goals

-   Preserve an authoritative, editable hymn catalog with multilingual lyrics, categories, and musical attributes.
-   Provide admins with secure CRUD workflows, role management, and bulk import utilities to streamline content operations.
-   Deliver low-latency user endpoints with pagination, search filters, and caching to support mobile clients offline-first behaviors.
-   Enforce API authentication, validation, and policy checks that respect role-based authorization boundaries.
-   Expose application version checks so client apps can prompt updates when minimum versions change.
-   Ensure the codebase remains testable, maintainable, and ready for future features such as audio streaming or analytics integrations.
