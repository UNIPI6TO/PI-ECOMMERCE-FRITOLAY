terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/sa"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
    google-beta = { source = "hashicorp/google-beta", version = "~> 5.0" }
  }
}
provider "google" { project = "project-3e1faa58-1e7d-4e8d-933", region = "us-central1" }
provider "google-beta" { project = "project-3e1faa58-1e7d-4e8d-933", region = "us-central1" }

data "google_project" "project" {}

resource "google_service_account" "backend_sa" {
  account_id   = "sa-backend"
  display_name = "Backend SA"
}
resource "google_service_account" "frontend_sa" {
  account_id   = "sa-frontend"
  display_name = "Frontend SA"
}

resource "google_project_iam_member" "backend_roles" {
  for_each = toset([
    "roles/cloudsql.client",
    "roles/storage.objectAdmin",
    "roles/datastore.user",
    "roles/secretmanager.secretAccessor"
  ])
  project = data.google_project.project.project_id
  role    = each.key
  member  = "serviceAccount:${google_service_account.backend_sa.email}"
}

resource "google_project_iam_member" "frontend_roles" {
  project = data.google_project.project.project_id
  role    = "roles/storage.objectViewer"
  member  = "serviceAccount:${google_service_account.frontend_sa.email}"
}

resource "google_project_service_identity" "sql_sa" {
  provider = google-beta
  service  = "sqladmin.googleapis.com"
}
resource "google_project_service_identity" "storage_sa" {
  provider = google-beta
  service  = "storage.googleapis.com"
}
resource "google_project_service_identity" "firestore_sa" {
  provider = google-beta
  service  = "firestore.googleapis.com"
}

resource "google_project_iam_member" "kms_sql" {
  project = data.google_project.project.project_id
  role    = "roles/cloudkms.cryptoKeyEncrypterDecrypter"
  member  = "serviceAccount:${google_project_service_identity.sql_sa.email}"
}
resource "google_project_iam_member" "kms_storage" {
  project = data.google_project.project.project_id
  role    = "roles/cloudkms.cryptoKeyEncrypterDecrypter"
  member  = "serviceAccount:${google_project_service_identity.storage_sa.email}"
}
resource "google_project_iam_member" "kms_firestore" {
  project = data.google_project.project.project_id
  role    = "roles/cloudkms.cryptoKeyEncrypterDecrypter"
  member  = "serviceAccount:${google_project_service_identity.firestore_sa.email}"
}
