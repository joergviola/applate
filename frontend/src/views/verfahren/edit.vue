<template>
  <v-card flat>

    <v-toolbar class="elevation-10" color="primary" dark>
      <v-btn icon class="hidden-xs-only">
        <v-icon>arrow_back</v-icon>
      </v-btn>

      <v-toolbar-title>Verfahren</v-toolbar-title>
      <v-spacer></v-spacer>
      <div class="caption">
        nach §30 Datenschutzgrundverordnung (DSGVO) zum Verfahrensregister<br>
        bei dem/der betrieblichen Beauftragten für den Datenschutz</div>

      <v-spacer></v-spacer>

      <v-btn icon class="hidden-xs-only">
        <v-icon>more_vert</v-icon>
      </v-btn>
    </v-toolbar>
      <v-card class="elevation-10">

        <v-card-text>
          <v-text-field v-model="item.name" label="Name des Verfahrens"></v-text-field>


          <v-expansion-panel
                  v-model="panel"
                  expand
          >
            <v-toolbar class="elevation-0"  @click="()=>toggle(0)" color="primary" dark>
              <v-icon>home</v-icon>
              <v-toolbar-title>
                Stammdaten
              </v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[0]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[0]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-container>
                <v-form>
                  <v-textarea
                          v-model="item.organisation"
                          label="Verantwortliche Stelle"
                          rows="1"
                          auto-grow
                  ></v-textarea>
                  <v-textarea
                          v-model="item.purpose"
                          label="Zweck der Datenerhebung"
                          hint="Nach §13 dürfen nur notwendige Daten erhoben werden."
                          rows="1"
                          auto-grow
                  ></v-textarea>
                  <v-textarea
                          v-model="item.receiver"
                          label="Empfänger oder Kategorien von Empfängern, denen die Daten mitgeteilt werden"
                          rows="1"
                          auto-grow
                  ></v-textarea>
                  <v-textarea
                          label="Regelfristen für die Löschung"
                          v-model="item.deletion"
                          rows="1"
                          auto-grow
                  ></v-textarea>
                </v-form>
              </v-container>
            </v-expansion-panel-content>

            <v-toolbar class=" elevation-0"  @click="()=>toggle(1)" color="primary" dark>
              <v-icon>info</v-icon>
              <v-toolbar-title>Datenschutz-Hinweis</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[1]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[1]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-form>
                <v-container>
                  <v-textarea
                          label="Datenschutz-Hinweis für den Nutzer"
                          v-model="item.privacy"
                          hint="Wird bei den Datenschutz-Hinweisen veröffentlicht."
                          rows="4"
                          auto-grow
                  ></v-textarea>
                </v-container>
                </v-form>
            </v-expansion-panel-content>

            <v-toolbar class=" elevation-0"  @click="()=>toggle(2)" color="primary" dark>
              <v-icon>group</v-icon>
              <v-toolbar-title>Betroffene Personen</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[2]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[2]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-form>
                <v-container>
                  <v-layout row wrap  v-for="role in roles">
                      <v-flex xs12 sm3 md3>
                        <v-switch :label="role.label" v-model="item.roles[role.name]"></v-switch>
                      </v-flex>
                      <v-flex xs12 sm9 md9>
                        <v-textarea v-if="item.roles[role.name]"
                                    label="Beschreibung der Daten oder Datenkategorien"
                                    v-model="item.roles[role.name+'_description']"
                                    rows="1"
                                    auto-grow
                        ></v-textarea>
                      </v-flex>
                  </v-layout>
                </v-container>
              </v-form>
            </v-expansion-panel-content>

            <v-toolbar class="elevation-0"  @click="()=>toggle(3)" color="primary" dark>
              <v-icon>cloud</v-icon>
              <v-toolbar-title>Auftragsdatenverarbeitung</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[3]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[3]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-form>
                <v-container>
                  <v-layout row wrap>
                    <v-flex xs12 sm12 md12>
                      <v-switch v-model="item.processor.applicable" label="Delegation an einen Auftragsdatenverarbeiter"></v-switch>
                    </v-flex>
                    <v-flex xs12 sm12 md12 v-if="item.processor.applicable">
                      <v-flex xs12 sm12 md12>
                        <v-textarea
                                label="Empfänger, dem Daten mitgeteilt werden (inkl. Adresse)"
                                v-model="item.processor.address"
                                rows="1"
                                auto-grow
                        ></v-textarea>
                      </v-flex>
                      <v-flex xs6 sm3 md3>
                        <v-switch v-model="item.processor.employees" label="Beschäftigte"></v-switch>
                      </v-flex>
                      <v-flex xs6 sm3 md3>
                        <v-switch v-model="item.processor.sub" label="Subunternehmer"></v-switch>
                      </v-flex>
                      <v-flex xs12 sm4 md4>
                        <v-switch v-model="item.processor.third" label="Datenübermittlung in Drittstaaten" ></v-switch>
                      </v-flex>
                      <v-flex v-if="item.processor.third" xs12 sm12 md12>
                        <v-text-field v-model="item.processor.third_country" label="Land"></v-text-field>
                        <v-textarea
                                label="Art der Daten"
                                v-model="item.processor.third_data"
                                rows="1"
                                auto-grow
                        ></v-textarea>
                      </v-flex>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-form>
            </v-expansion-panel-content>


            <v-toolbar class="elevation-0"  @click="()=>toggle(4)" color="primary" dark>
              <v-icon>computer</v-icon>
              <v-toolbar-title>Eingesetzte Datenverarbeitung</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[4]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[4]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-container>
                <v-form>
                  <v-textarea v-model="item.system" label="Art der eingesetzten DV-Anlagen und Software"
                              rows="4"
                              auto-grow
                  ></v-textarea>
                </v-form>
              </v-container>
            </v-expansion-panel-content>

            <v-toolbar class="elevation-0"  @click="()=>toggle(5)" color="primary" dark>
              <v-icon>question_answer</v-icon>
              <v-toolbar-title>Datenschutz-Anfragen</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[5]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[5]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-container>
                <v-form>
                  <v-container>
                    <v-layout row wrap v-for="right in rights">
                      <v-flex xs12 sm9 md9>
                        <v-textarea
                                :label="right.label"
                                v-model="item.rights[right.name]"
                                rows="1"
                                auto-grow
                        ></v-textarea>
                      </v-flex>
                      <v-flex xs12 sm3 md3>
                        <v-select
                                v-if="users"
                                :items="users"
                                v-model="item.rights[right.name+'_responsible']"
                                menu-props="auto"
                                label="Zuständig"
                                prepend-icon="person"
                                single-line
                        ></v-select>
                        <v-text-field v-if="!users" v-model="item.rights[right.name+'_responsible']"></v-text-field>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-form>
              </v-container>
            </v-expansion-panel-content>

            <v-toolbar class="elevation-0"  @click="()=>toggle(6)" color="primary" dark>
              <v-icon>check</v-icon>
              <v-toolbar-title>Technische & Organisatorische Maßnahmen</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon v-if="!panel[6]">keyboard_arrow_down</v-icon>
                <v-icon v-if="panel[6]">keyboard_arrow_up</v-icon>
              </v-btn>
            </v-toolbar>
            <v-expansion-panel-content>
              <v-container>
                <v-form>
                  <v-container>
                    <v-layout row wrap>
                      <v-flex xs7 sm3 md3>Maßnahme</v-flex>
                      <v-flex xs5 sm3 md3>Zuständig</v-flex>
                      <v-flex xs12 sm6 md6>Beschreibung</v-flex>
                    </v-layout>
                    <v-layout row wrap v-for="action in actions">
                      <v-flex xs7 sm3 md3>
                        <v-switch :label="action.label" v-model="item.actions[action.name]"></v-switch>
                      </v-flex>
                      <v-flex xs5 sm3 md3>
                        <v-select v-if="users && item.actions[action.name]"
                                  v-model="item.actions[action.name+'_responsible']"
                                :items="users"
                                menu-props="auto"
                        ></v-select>
                        <v-text-field v-if="!users" v-model="item.actions[action.name+'_responsible']"></v-text-field>
                      </v-flex>
                      <v-flex xs12 sm6 md6>
                        <v-textarea v-if="item.actions[action.name]"
                                    v-model="item.actions[action.name+'_description']"
                                    rows="1"
                                    auto-grow
                        ></v-textarea>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-form>
              </v-container>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" flat @click="save">Herunterladen</v-btn>
        </v-card-actions>
      </v-card>
  </v-card>
</template>

<script>

  import api from "../../lib/api"
  import * as docx from "docx";
  import { saveAs } from 'file-saver';

  export default {

    data: () => ({
      users: null,//['M. Dudek', 'F. Hansen', 'R. Reimann'],
      panel:[],
      item: {
        roles: {},
        processor: {},
        rights: {},
        actions: {}
      },
      roles: [
        {name: 'kunden', label:'Kunden'},
        {name: 'mitarbeiter', label:'Mitarbeiter'},
        {name: 'website', label:'Website-Besucher'},
      ],
      rights: [
        {name: 'access', label:'Recht auf Zugriff'} ,
        {name: 'correction', label:'Recht auf Korrektur'} ,
        {name: 'erasure', label:'Recht auf Vergessen'} ,
        {name: 'confine', label:'Recht auf Beschränkung'} ,
        {name: 'export', label:'Recht auf Export'} ,
        {name: 'denial', label:'Recht auf Widerspruch'} ,
        {name: 'automated', label:'Recht auf Untersagung automtisierter Verarbeitung'} ,
      ],
      actions: [
        {name: 'enter1', label:'Zutrittskontrolle'} ,
        {name: 'enter2', label:'Zugangskontrolle'} ,
        {name: 'access', label:'Zugriffskontrolle'} ,
        {name: 'transfer', label:'Weitergabekontrolle'} ,
        {name: 'input', label:'Eingabekontrolle'} ,
        {name: 'order', label:'Auftragskontrolle'} ,
        {name: 'availability', label:'Verfügbarkeitskontrolle'} ,
        {name: 'seperation', label:'Trennungsgebot'} ,
      ],
    }),

    computed: {
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    created () {
      this.initialize()
    },

    methods: {
      initialize () {
        api.get('verfahren', this.$route.params.id)
          .then(item => this.item = item)
      },
      toggle(no) {
        this.panel = this.panel.slice()
        this.panel[no] = !this.panel[no]
      },
      cancel() {},

      generateDocument() {
        // Create document
        var doc = new docx.Document();

        var table = rawTable(2, 1)
        var cell = table.getCell(0, 0);
        cell.addContent(heading("Verfahrensverzeichnis nach § 4g Bundesdatenschutzgesetz (BDSG)\n" +
                "zum Verfahrensregister \n" +
                "bei dem/der betrieblichen Beauftragten für den Datenschutz\n"));
        cell = table.getCell(1, 0);
        cell.addContent(new docx.Paragraph("Anlage \n" +
                "\tVersion 1: "+this.item.name+"\n"));

        stdTable("Verantwortliche Stelle:", this.item.organisation)

        stdTable("Zweckbestimmungen der Datenerhebung, -verarbeitung oder –nutzung:", this.item.purpose)

        const roles = this.roles
                .filter(r => this.item.roles[r.name])
                .map(r=>({role:r.label, description: this.item.roles[r.name+'_description']}))

        table = rawTable(1+roles.length, 1)
        var cell = table.getCell(0, 0);
        cell.addContent(label("Betroffene Personengruppen und Daten oder Datenkategorien"))
        roles.forEach((r, i) => addStdContent(table, i+1, 0, (i+1) + ". " + r.role, r.description))

        stdTable("Empfänger oder Kategorien von Empfängern, denen die Daten mitgeteilt werden können:",
                this.item.receiver)

        stdTable("Regelfristen für die Sperrung und Löschung der Daten:",
                this.item.deletion)

        if (this.item.processor.applicable) {
          table = rawTable(3, 1)
          var cell = table.getCell(0, 0);
          cell.addContent(label("Geplante Datenübermittlung an Auftragsdatenvermittler:"))
          addStdContent(table, 1, 0, "Organisation", this.item.processor.address)
          addStdContent(table, 2, 0, "Personen",
                  (this.item.processor.employees ? "Beschäftigte" : "") +
                  (this.item.processor.employees && this.item.processor.sub ? " und " : "") +
                  (this.item.processor.sub ? "Subunternehmer" : "")
          )
          if (this.item.processor.third) {
            table = rawTable(3, 1)
            var cell = table.getCell(0, 0);
            cell.addContent(label("Geplante Datenübermittlung in Drittstaaten:"))
            addStdContent(table, 1, 0, "Names des Drittstaates", this.item.processor.third_country)
            addStdContent(table, 2, 0, "Art der Daten oder Datenkategorien ", this.item.processor.third_data)
          }
        }

        table = rawTable(3+this.actions.length, 3)
        table.getRow(0).mergeCells(0, 2);
        table.getRow(1).mergeCells(0, 2);
        table.getRow(2).mergeCells(0, 2);
        var cell = table.getCell(0, 0);
        cell.addContent(label("Angaben zur Beurteilung der Angemessenheit getroffener Sicherheitsmaßnahmen:"))
        addStdContent(table, 1, 0, "Art der eingesetzten DV-Anlagen und Software ", this.item.systems);
        var cell = table.getCell(2, 0);
        cell.addContent(label("Maßnahmen nach § 32 DSGVO:"))
        this.actions.forEach((a, i) => {
          cell = table.getCell(3+i, 1)
          cell.addContent(label(a.label))
          if (this.item.actions[a.name]) {
            cell = table.getCell(3+i, 0)
            cell.addContent(label("X"))
            cell = table.getCell(3+i, 2)
            cell.addContent(label(this.item.actions[a.name+'_responsible']))
          }
        })

        doc.createParagraph("Alle Angaben in diesem Verfahrensverzeichnis entsprechen dem heutigen Informationsstand (soweit bekannt gegeben und erkennbar).")

        var p = new docx.Paragraph()
        p.createTextRun("                                                                          ").underline()
                .break()
        p.createTextRun("(Ort, Datum, Unterschrift Datenschutzbeauftragter) ")
        doc.addParagraph(p)
        return doc;

        function rawTable(rows, cols) {
          var table = doc.createTable(rows, cols)
          table.Properties.CellMargin.addLeftMargin(500, docx.WidthType.DXA)
          table.Properties.CellMargin.addRightMargin(500, docx.WidthType.DXA)
          table.Properties.CellMargin.addTopMargin(100, docx.WidthType.DXA)
          table.Properties.CellMargin.addBottomMargin(100, docx.WidthType.DXA)
          table.setWidth(docx.WidthType.PERCENTAGE, "100%")
          doc.createParagraph(" ")
          return table;
        }

        function heading(text) {
          return new docx.Paragraph(text).heading1().center()
        }

        function label(text) {
          return new docx.Paragraph(text)
        }

        function input(text) {
          const p = new docx.Paragraph()
          p.createTextRun(text).font("Courier New")
          return p
        }

        function stdTable(l, i) {
          const table = rawTable(1, 1)
          addStdContent(table, 0, 0, l, i);
        }

        function addStdContent(table, row, col, l, i) {
          const cell = table.getCell(row, col);
          cell.addContent(label(l))
          cell.addContent(input(i))
        }
      },
      save() {
        console.log(JSON.stringify(this.item))

        const doc = this.generateDocument()

        const packer = new docx.Packer();

        packer.toBlob(doc).then((blob) => {
          // saveAs from FileSaver will download the file
          saveAs(blob, "verfahren.docx");
        });
      },
    }
  }
</script>