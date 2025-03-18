package com.itportal.model;

public enum BioDataType {
    ECG("ЭКГ"),
    PPG("Фотоплетизмография"),
    EEG("ЭЭГ"),
    RESPIRATION_RATE("Частота дыхания");

    private final String description;

    BioDataType(String description) {
        this.description = description;
    }

    public String getDescription() {
        return description;
    }
} 