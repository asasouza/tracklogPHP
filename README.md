# TracklogPHP

TracklogPHP é uma biblioteca escrita na linguagem PHP com o objetivo de analisar e converter arquivos de GPS. </br>
A biblioteca possui uma interface gráfica disponível em https://tracklog-php.herokuapp.com/, que demonstra graficamente algumas funcionalidades do projeto.
## Documentação

Esta biblioteca foi desenvolvivida como parte de minha monografia para a conclusão do curso de Análise e Desenvolvimento de Sistemas, pelo Instituto Federal de Educação, Ciência e Tecnologia do Estado de São Paulo, e a monografia completa, com as etapas de desenvolvimento, testes e conclusões, pode ser encontrada <a href="https://drive.google.com/file/d/13ez6muQ8gAQKKJSF3xY5uqhJTatvD85b/view?usp=sharing">aqui</a>.

Atualmente a biblioteca suporta os seguintes formatos: KML, GPX, TCX, CSV e GeoJSON.

### Métodos da biblioteca
| Métodos 	| Descrição 	|
|:------------------------------------------------------:	|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:	|
| (protegida/abstrata) __construct($file) 	| Método delegado às classes concretas dos arquivos para leitura das estruturas e obtenção dos dados.  Parâmetro de entrada: o caminho do arquivo a ser lido.  Retorna um objeto do tipo do arquivo. Exceções: quando o arquivo não possui dados correspondentes a um  tracklog. 	|
| (protegida/abstrata) write($file_path) 	| Método delegado às classes concretas para gerar a estrutura do arquivo de tracklog no formato correspondente a classe. Utilizado no método  write. Parâmetro de entrada: caminho a qual o arquivo pode ser salvo.  Retorna um texto contendo a estrutura do arquivo. 	|
| (protegida/abstrata) validate($file) 	| Método delegado às classes concretas para verificar a validade do arquivo correspondente a classe.  Parâmetro de entrada: o arquivo à ser validado.  Exceções: quando o arquivo indicado não existe; quando o arquivo é inválido. 	|
| (protegida) populateDistance() 	| Insere nos objetos trackPoint as suas respectivas distâncias quando atingidos no tracklog. Utilizada no método  __construct. 	|
| (protegida) hasTime() 	| Verifica se o tracklog criado possui dados referentes ao tempo dos trackPoints. Utilizada nos métodos getTimes , getPace, getPaces, getAverageSpeed, getAverageSpeeds e write. Retorna verdadeiro, caso haja, ou falso, caso não possua. 	|
| (protegida) hasElevation() 	| Verifica se o tracklog criado possui dados referente a elevação dos trackPoints. Utilizada nos métodos getElevations, getMaxElevation, getElevationGain, getElevationLoss e write. Retorna verdadeiro, caso haja, ou falso, caso não possua. 	|
| (protegida) hasDistance() 	| Verifica se o tracklog criado possui dados referente a distância dos trackPoints. Utilizada no método write. Retorna verdadeiro, caso haja, ou falso, caso não possua. 	|
| (privada) haversineFormula($latB, $lonB, $latE, $lonE) 	| Utiliza a fórmula de Haversine para calcular a distância entre dois pontos de latitude e longitude. Utilizada no método populateDistances. Parâmetros de entrada: latitude e longitude de início e latitude e longitude do final. Retorna o valor da distância entre os dois pontos calculado. 	|
| (privada) smoothArray($array) 	| Utiliza o filtro de média móvel para suavizar vetores que contenham ruídos de informação. Utilizada nos métodos  getPaces e getAverageSpeeds. Parâmetro de entrada: vetor a ser suavizado. Retorna o vetor com os valores suavizados. 	|
| (pública) getPoints() 	| Método utilizado para obter todos os pontos de um tracklog. Retorna um vetor associativo ordenado com os todos os dados disponíveis de todos os trackPoints associados ao tracklog. 	|
| (pública) getLatitudes() 	| Método utilizado para obter todas as latitudes de um tracklog. Retorna um vetor ordenado contendo todos os dados de latitude de todos os trackPoints associados ao tracklog. 	|
| (pública) getLongitudes() 	| Método utilizado para obter todas as longitudes de um tracklog. Retorna um vetor ordenado contendo todos os dados de longitude dos trackPoints associados ao tracklog. 	|
| (pública) getElevations() 	| Método utilizado para obter todas as elevações de um tracklog. Utilizado no método getMaxElevation. Retorna um vetor ordenado contendo todos os dados de altitude dos trackPoints associados ao tracklog. Exceção: quando o tracklog criado não possui dados de elevação. 	|
| (pública) getTimes() 	| Método utilizado para obter todas os tempos de um tracklog. Retorna um vetor ordenado contendo todos os dados de tempo dos trackPoints associados ao tracklog. Exceção: quando o tracklog criado não possui dados de tempo. 	|
| (pública) getDistances() 	| Método utilizado para obter todas as distâncias de um tracklog. Retorna um vetor ordenado contendo todos os dados de distância de todos os trackPoints associados ao tracklog. 	|
| (pública) getPace() 	| Método utilizado para obter o ritmo médio realizado para concluir o tracklog. Retorna o ritmo médio realizado no percurso em minutos por quilômetro. Exceção: quando o tracklog criado não possui dados referentes ao tempo do percurso. 	|
| (pública) getPaces($unit, $smoothed) 	| Método utilizado para obter o ritmo realizado no percurso para cada ponto do tracklog. Parâmetros de entrada: a unidade de medida do ritmo (enum: ['seconds', 'minutes']); se o vetor retornado precisa ser suavizado pela função  smoothArray. Retorna um vetor ordenado contendo o ritmo de realização do percurso entre um ponto de tracklog e o seguinte. Exceção: quando o tracklog criado não possui dados referentes ao tempo do percurso. 	|
| (pública) getAverageSpeed() 	| Método utilizado para obter a velocidade média realizada para concluir o tracklog. Retorna a velocidade média realizada no percurso em quilômetros por hora. Exceção: quando o tracklog criado não possui dados referentes ao tempo do percurso. 	|
| (pública) getAverageSpeeds($smoothed) 	| Método utilizado para obter a velocidade média no percurso para cada ponto do tracklog. Parâmetros de entrada: se o vetor retornado precisa ser suavizado pela função smoothArray. Retorna um vetor ordenado contendo a velocidade média de realização do percurso entre um ponto de tracklog e o seguinte. Exceção: quando o tracklog criado não possui dados referentes ao tempo do percurso. 	|
| (pública) getTotalDistance($unit) 	| Método utilizado para obter a distância total do percurso no tracklog. Utilizado no método getPace e getAverageSpeed. Parâmetros de entrada: unidade de medida do retorno (enum: ['kilometers', 'meters', 'miles']). Padrão em metros. Retorna a distância total do percurso na unidade de medida escolhida. 	|
| (pública) getTotalTime($unit) 	| Método utilizado para obter o tempo total gasto para realizar o percurso. Utilizado no método getPace e getAverageSpeed. Parametros de entrada: unidade de medida do retorno (enum: ['seconds', 'minutes', 'hours']). Padrão em timestamp. Retorna o tempo total gasto para realizar o percurso na unidade de medida escolhida. 	|
| (pública) getTrackName() 	| Método utilizado para obter o nome do tracklog. Retorna o nome do tracklog. 	|
| (pública) getMaxElevation() 	| Método utilizado para obter a altura máxima atingida no percurso. Retorna o valor da altura máxima em metros. Exceção:  quando o tracklog criado não possui dados referentes a elevação do percurso. 	|
| (pública) getElevationGain() 	| Método utilizado para obter o ganho de altitude na realização do percurso. Retorna o valor total do ganho de altitude do percurso. Exceção:  quando o tracklog criado não possui dados referentes a elevação do percurso. 	|
| (pública) getElevationLoss() 	| Método utilizado para obter o perda de altitude na realização do percurso. Retorna o valor total da perda de altitude do percurso. Exceção:  quando o tracklog criado não possui dados referentes a elevação do percurso. 	|
| (pública) out($output, $file_path) 	| Método utilizado para converter uma estrutura de arquivos em outra. Parâmetros de entrada: formato desejado de saída (enum: ['kml', 'gpx', 'tcx', 'csv', 'geojson']); caminho para salvar o arquivo convertido. Retorna um texto contendo a estrutura do arquivo. Exceção: caso o formato de saída escolhido não seja suportado pela biblioteca. 	|

### Exemplo

```php
require_once('lib/tracklogPhp.main.php');

$file = 'caminho/para/o/arquivo.kml';

$tracklog = new KML($file); // As classes devem ser utilizadas de acordo com o tipo de arquivo;

// Obtém a distância total do tracklog.
$tracklog->getTotalDistance('kilometers')

// Converte o arquivo em KML para GPX
$tracklog->out('gpx', 'caminho/para/salvar/o/novo/arquivo');

```
Outros exemplos de utilização da biblioteca podem ser encontradas nos arquivos `tests.php` ou `index.php`.

