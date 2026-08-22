terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/secret_manager"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
  }
}
provider "google" {
  project = "project-3e1faa58-1e7d-4e8d-933"
  region  = "us-central1"
}

resource "google_secret_manager_secret" "backend_env" {
  secret_id = "fritolay-backend-env"
  replication {
    auto {}
  }
}

resource "google_secret_manager_secret" "frontend_env" {
  secret_id = "fritolay-frontend-env"
  replication {
    auto {}
  }
}
