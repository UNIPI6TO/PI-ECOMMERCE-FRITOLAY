terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/firebase"
  }
  required_providers {
    google = { 
      source  = "hashicorp/google" 
      version = "~> 5.0" 
    }
  }
}

provider "google" {
  project = "project-3e1faa58-1e7d-4e8d-933"
  region  = "us-central1"
}

data "google_project" "project" {}

# Habilitar el servicio de Firebase en GCP
resource "google_project_service" "firebase" {
  project = data.google_project.project.project_id
  service = "firebase.googleapis.com"

  disable_on_destroy = false
}

# Configuración del proyecto de Firebase ligado al proyecto GCP
resource "google_firebase_project" "default" {
  provider = google
  project  = data.google_project.project.project_id

  depends_on = [
    google_project_service.firebase
  ]
}

# Creación de la Aplicación Web en Firebase
resource "google_firebase_web_app" "frontend_app" {
  provider     = google
  project      = data.google_project.project.project_id
  display_name = "Fritolay Frontend Web App"

  depends_on = [
    google_firebase_project.default
  ]
}

# Generación de la API Key para el SDK Web de Firebase
resource "google_apikeys_key" "firebase_web_key" {
  name         = "firebase-web-sdk-key"
  display_name = "Firebase Web SDK Key (Terraform)"
  project      = data.google_project.project.project_id

  restrictions {
    api_targets {
      service = "firestore.googleapis.com"
    }
    api_targets {
      service = "firebase.googleapis.com"
    }
  }
}
