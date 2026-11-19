# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- Students consult academic offerings, choose available groups, and review their enrollment.
- University staff register and search enrollments by student and academic period.
- Administrators maintain institutional users, roles, and academic catalogs.

## Product Purpose

Centralize academic enrollment work and give each institutional role a clear place to complete its tasks. Success means accurate, traceable enrollment information and a student who can understand and review their own registration.

## Positioning

A role-based academic enrollment system that connects students, staff, and administrators to the same program, course, period, group, and enrollment records.

## Operating Context

The application supports a Colombian higher-education context. Users access it through a browser while managing academic records and enrollment periods. Personal information and access to student records must remain appropriately restricted by role.

## Capabilities and Constraints

- Authentication is provided through Laravel Fortify.
- The system has Administrador, Funcionario, and Estudiante roles.
- Administrators maintain users and academic catalog records.
- Students consult the academic offering and manage their own enrollments.
- Administrators and staff register and search enrollment records.
- Group capacity and academic periods are part of the enrollment workflow.
- The application uses Laravel, Blade, Tailwind CSS v4, and MySQL-compatible migrations.
- Avoid implying that academic modules are available unless their corresponding workflow is implemented.

## Brand Commitments

- The user confirmed **Saint Victoria Academy** as the institutional name and approved the existing logo in `public/images/saint-victoria-academy-logo.png`.
- The interface should feel institutional while retaining a distinctive and creative academic character.

## Evidence on Hand

- The repository documents enrollment requirements and includes sample role accounts and academic records for local development.
- No production customers, testimonials, outcome metrics, or institutional accreditations have been provided; do not invent them.

## Product Principles

- Give students, staff, and administrators role-appropriate access to the same academic process.
- Make enrollment status and available academic information understandable.
- Protect student records through explicit access boundaries.
- Keep institutional content factual and traceable.
