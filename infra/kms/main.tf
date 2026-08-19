terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/kms"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
  }
}
provider "google" {
  project = "project-3e1faa58-1e7d-4e8d-933"
  region  = "us-central1"
}

resource "google_kms_key_ring" "keyring" {
  name     = "fritolay-keyring"
  location = "us-central1"
}
resource "google_kms_crypto_key" "mysql_key" {
  name     = "mysql-key"
  key_ring = google_kms_key_ring.keyring.id
  purpose  = "ENCRYPT_DECRYPT"
}
resource "google_kms_crypto_key" "gcs_key" {
  name     = "gcs-key"
  key_ring = google_kms_key_ring.keyring.id
  purpose  = "ENCRYPT_DECRYPT"
}
resource "google_kms_crypto_key" "firestore_key" {
  name     = "firestore-key"
  key_ring = google_kms_key_ring.keyring.id
  purpose  = "ENCRYPT_DECRYPT"
}

import {
  to = google_kms_key_ring.keyring
  id = "projects/project-3e1faa58-1e7d-4e8d-933/locations/us-central1/keyRings/fritolay-keyring"
}

import {
  to = google_kms_crypto_key.mysql_key
  id = "projects/project-3e1faa58-1e7d-4e8d-933/locations/us-central1/keyRings/fritolay-keyring/cryptoKeys/mysql-key"
}

import {
  to = google_kms_crypto_key.gcs_key
  id = "projects/project-3e1faa58-1e7d-4e8d-933/locations/us-central1/keyRings/fritolay-keyring/cryptoKeys/gcs-key"
}

import {
  to = google_kms_crypto_key.firestore_key
  id = "projects/project-3e1faa58-1e7d-4e8d-933/locations/us-central1/keyRings/fritolay-keyring/cryptoKeys/firestore-key"
}
