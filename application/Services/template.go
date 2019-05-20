package Services

import (
	"bytes"
	"html/template"
	"io/ioutil"
	"strings"
	"xapimanager/config"
)

func GetFgetorTemplate(templatefile string, data map[string]interface{}) string {

	file := config.GetGlobal().TEMPLATE_PATH + "/" + templatefile

	buf := new(bytes.Buffer)
	if contents, err := ioutil.ReadFile(file); err == nil {
		//因为contents是[]byte类型，直接转换成string类型后会多一行空格,需要使用strings.Replace替换换行符
		newContents := strings.Replace(string(contents), "\n", "", 1)
		var tmpl = template.Must(template.New("").Parse(newContents))
		tmpl.Execute(buf, data)
	}

	return buf.String()

}
