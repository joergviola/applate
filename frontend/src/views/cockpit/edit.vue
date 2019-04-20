<template>
    <v-card flat>

        <v-toolbar class="elevation-10" dark color="primary">
            <v-toolbar-title>Datenschutzportal von dsgvo-portal.de</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-btn flat @click="tab=1">Willkommen!</v-btn>
                <v-btn flat @click="tab=2">Datenschutzhinweise</v-btn>
                <v-btn flat @click="tab=3">Kontakt</v-btn>
            </v-toolbar-items>
        </v-toolbar>

        <v-container>
            <v-card flat v-if="tab==1" transition="slide-x-transition">
                <v-layout row wrap align-center>
                    <v-flex xs12 sm6 md6>
                        <img width="100%" src="../../../public/img/datenschutz.jpg">
                    </v-flex>
                    <v-flex xs12 sm6 md6>
                        <h1>Herzlich willkommen auf unserer Datenschutz-Seite</h1>
                        <p>...und vielen Dank für Ihr Interesse!</p>
                        <p>Wir nehmen den Schutz Ihrer Daten absolut ernst. Daher betreiben wir dieses Portal.
                            Hier erarbeiten alle unsere betroffenen Mitarbeiter stets neue Richtlinien zum Umgang mit
                            Ihren
                            Daten.</p>
                        <p>Und wir informieren Sie darüber, <a href="#" @click="tab=2">wie wir mit Ihren Daten
                            umgehen</a>.</p>
                        <p>Hier stehen wir Ihnen auch Rede und Antwort für jede <a href="#" @click="tab=2">Frage</a>,
                            die Sie zum Thema Datenschutz bei uns
                            haben.</p>
                    </v-flex>
                </v-layout>
            </v-card>
            <v-card flat v-if="tab==2" transition="slide-x-transition">
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm4 md4>
                                <img width="100%" src="../../../public/img/datenschutz-hinweise.jpg">
                            </v-flex>
                            <v-flex xs12 sm8 md8>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12>
                                        <h2>
                                            Unsere Datenschutzhinweise für Sie:
                                            <div class="body-1 right">
                                                <a href="#" v-if="open" @click="open=false">Alle schliessen</a>
                                                <a href="#" v-if="!open" @click="open=true">Alle öffnen</a>
                                            </div>
                                        </h2>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <collapsable-card title="Grundlegendes" :open="open">
                                            <p>
                                                Diese Datenschutzerklärung soll die Nutzer dieser Website über die Art, den Umfang und den Zweck der Erhebung und Verwendung personenbezogener Daten durch den Websitebetreiber Jörg Viola informieren.
                                                Ich nehme Ihren Datenschutz sehr ernst und behandelt Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Vorschriften.
                                                Da durch neue Technologien und die ständige Weiterentwicklung dieser Webseite Änderungen an dieser Datenschutzerklärung vorgenommen werden können, empfehle ich Ihnen sich die Datenschutzerklärung in regelmäßigen Abständen wieder durchzulesen.
                                                Definitionen der verwendeten Begriffe (z.B. “personenbezogene Daten” oder “Verarbeitung”) finden Sie in Art. 4 DSGVO.
                                            </p>
                                        </collapsable-card>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12 v-for="info in infos">
                                        <collapsable-card :title="info.title" :open="open">
                                            <span v-html="info.body"></span>
                                        </collapsable-card>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
            </v-card>
            <v-card flat v-if="tab==3" transition="slide-x-transition">
                <v-card-text>
                    <v-container>
                        <v-layout row wrap align-center>
                            <v-flex xs12 sm6 md6>
                                <v-select
                                        :items="rights.map(r=>r.label)"
                                        label="Wie können wir Ihnen helfen?"
                                        v-model="right"
                                ></v-select>
                                <v-text-field v-model="user" label="Bitte geben Sie Ihre E-Mail-Adresse an:"></v-text-field>
                                <v-textarea
                                            label="Haben Sie noch eine Anmerkung?"
                                            v-model="message"
                                            rows="1"
                                            auto-grow
                                ></v-textarea>
                                <v-btn color="primary" :disabled="right==null || user==null" @click="send">Senden</v-btn>
                                <v-alert :value="mailSent" type="success" color="primary" dark>
                                    Vielen Dank für Ihre Anfrage, wir melden uns in Kürze bei Ihnen.
                                </v-alert>

                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <img width="100%" src="../../../public/img/anliegen.jpg">
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-container>
    </v-card>
</template>

<script>

  import api from "../../lib/api"
  import CollapsableCard from "../../components/CollapsableCard"

  export default {

    components: {
      'collapsable-card': CollapsableCard
    },

    data: () => ({
      tab: 1,
      open: true,
      right: null,
      user: null,
      message: null,
      mailSent: false,
      infos: [],
      rights: [
        {value: 'access', label: 'Ich möchte die über mich bei Ihnen gespeicherten Daten abrufen'},
        {name: 'correction', label: 'Ich möchte, das Daten über mich korrigiert werden'},
        {name: 'erasure', label: 'Ich möchte, dass Sie meine Daten löschen'},
        {name: 'confine', label: 'Ich möchte bestimmen, wie genau Sie meine Daten verwenden dürfen'},
        {name: 'export', label: 'Ich fordere einen Export der Daten an, die Sie über mich haben'},
        {name: 'denial', label: 'Ich widerspreche der weiteren Verwendung meiner Daten'},
        {name: 'automated', label: 'Ich untersage Ihnen, meine Daten automatisiert zu verarbeiten'},
      ],
    }),

    computed: {},

    watch: {
    },

    created () {
      this.initialize()
    },

    methods: {
      initialize () {
        api.get('dsh', 'web')
          .then(data => this.infos = data)
      },
      send() {
        var Email = { send: function (a) { return new Promise(function (n, e) { a.nocache = Math.floor(1e6 * Math.random() + 1), a.Action = "Send"; var t = JSON.stringify(a); Email.ajaxPost("https://smtpjs.com/v3/smtpjs.aspx?", t, function (e) { n(e) }) }) }, ajaxPost: function (e, n, t) { var a = Email.createCORSRequest("POST", e); a.setRequestHeader("Content-type", "application/x-www-form-urlencoded"), a.onload = function () { var e = a.responseText; null != t && t(e) }, a.send(n) }, ajax: function (e, n) { var t = Email.createCORSRequest("GET", e); t.onload = function () { var e = t.responseText; null != n && n(e) }, t.send() }, createCORSRequest: function (e, n) { var t = new XMLHttpRequest; return "withCredentials" in t ? t.open(e, n, !0) : "undefined" != typeof XDomainRequest ? (t = new XDomainRequest).open(e, n) : t = null, t } };
        console.log("Sending", this.right, this.user)
        Email.send({
          SecureToken : "7dd29b8d-4943-4c63-b988-4e95de251efe",
          To : 'joerg@joergviola.de',
          From : "joerg@joergviola.de",
          Subject : "Datenschutzanfrage",
          Body : 'Recht: ' + this.right + '<br>Benutzer: ' + this.user + '<br>Nachricht: ' + this.message
        }).then(
          message => this.mailSent = true
        );
    },

    }
  }
</script>