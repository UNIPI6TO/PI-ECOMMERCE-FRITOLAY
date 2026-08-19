terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/gcs"
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

data "google_kms_crypto_key" "gcs_key" {
  name     = "gcs-key"
  key_ring = "projects/${data.google_project.project.project_id}/locations/us-central1/keyRings/fritolay-keyring"
}

resource "google_storage_bucket" "images_bucket" {
  name                        = "fritolay-images-${data.google_project.project.project_id}"
  location                    = "us-central1"
  force_destroy               = true
  uniform_bucket_level_access = true

  encryption {
    default_kms_key_name = data.google_kms_crypto_key.gcs_key.id
  }
}

import {
  to = google_storage_bucket.images_bucket
  id = "fritolay-images-project-3e1faa58-1e7d-4e8d-933"
}
