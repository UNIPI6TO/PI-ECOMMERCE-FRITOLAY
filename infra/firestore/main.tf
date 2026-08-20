terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/firestore"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
  }
}
provider "google" {
  project = "project-3e1faa58-1e7d-4e8d-933"
  region  = "us-central1"
}

data "google_project" "project" {}

data "google_kms_crypto_key" "firestore_key" {
  name     = "firestore-key"
  key_ring = "projects/${data.google_project.project.project_id}/locations/us-central1/keyRings/fritolay-keyring"
}

resource "google_firestore_database" "database" {
  project     = data.google_project.project.project_id
  name        = "(default)"
  location_id = "us-central1"
  type        = "FIRESTORE_NATIVE"
}


